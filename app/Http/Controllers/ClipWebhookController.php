<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentWebhookLog;
use App\Services\ClipService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class ClipWebhookController extends Controller
{
    public function __invoke(Request $request, ClipService $clip, OrderService $orders)
    {
        if ($request->isMethod('GET')) {
            return response()->json([
                'ok' => true,
                'provider' => 'clip',
                'message' => 'Webhook activo.',
            ]);
        }

        $raw = $request->getContent();
        $payload = $request->json()->all() ?: $request->all();
        $hash = hash('sha256', $raw ?: json_encode($payload));
        $signatureValid = $clip->validateSignature($request);
        $extracted = $clip->extract($payload);

        if (! $signatureValid) {
            if ($this->isDiagnosticPing($clip, $payload, $extracted)) {
                PaymentWebhookLog::firstOrCreate(
                    ['payload_hash' => $hash],
                    [
                        'provider' => 'clip',
                        'event_id' => $extracted['event_id'],
                        'payment_request_id' => $extracted['payment_request_id'],
                        'external_reference' => $extracted['external_reference'],
                        'status' => 'diagnostic_ping',
                        'payload' => $payload,
                        'signature_valid' => false,
                        'processed_at' => now(),
                        'response_status' => 200,
                    ]
                );

                return response()->json([
                    'ok' => true,
                    'provider' => 'clip',
                    'diagnostic' => true,
                ]);
            }

            $payment = $this->findPayment($extracted);

            if (! $payment) {
                PaymentWebhookLog::firstOrCreate(
                    ['payload_hash' => $hash],
                    [
                        'provider' => 'clip',
                        'event_id' => $extracted['event_id'],
                        'payment_request_id' => $extracted['payment_request_id'],
                        'external_reference' => $extracted['external_reference'],
                        'status' => 'unsigned_unmatched',
                        'payload' => $payload,
                        'signature_valid' => false,
                        'processed_at' => now(),
                        'response_status' => 200,
                    ]
                );

                return response()->json([
                    'ok' => true,
                    'provider' => 'clip',
                    'registered' => true,
                ]);
            }

            if ($extracted['payment_request_id']) {
                try {
                    $remotePayload = $clip->status($extracted['payment_request_id']);
                    $remoteExtracted = $this->mergeExtracted($extracted, $clip->extract($remotePayload));

                    if ($this->matchesPayment($payment, $remoteExtracted)) {
                        $order = $payment->order;

                        $log = PaymentWebhookLog::firstOrCreate(
                            ['payload_hash' => $hash],
                            [
                                'provider' => 'clip',
                                'event_id' => $remoteExtracted['event_id'],
                                'order_id' => $order?->id,
                                'payment_request_id' => $remoteExtracted['payment_request_id'],
                                'external_reference' => $remoteExtracted['external_reference'],
                                'status' => $remoteExtracted['status'] ?: 'unsigned_verified',
                                'payload' => [
                                    'received' => $payload,
                                    'verified' => $remotePayload,
                                ],
                                'signature_valid' => false,
                                'response_status' => 200,
                            ]
                        );

                        if ($order && ! $log->processed_at) {
                            $this->processPayment($clip, $orders, $payment, $order, $remoteExtracted, $remotePayload, $log);
                        }

                        return response()->json([
                            'ok' => true,
                            'provider' => 'clip',
                            'verified' => true,
                        ]);
                    }
                } catch (\Throwable) {
                    // Si Clip no permite consultar el estado en ese momento, se
                    // registra para revision manual sin modificar el pedido.
                }
            }

            PaymentWebhookLog::firstOrCreate(
                ['payload_hash' => $hash],
                [
                    'provider' => 'clip',
                    'event_id' => $extracted['event_id'],
                    'order_id' => $payment->order_id,
                    'payment_request_id' => $extracted['payment_request_id'],
                    'external_reference' => $extracted['external_reference'],
                    'status' => 'unsigned_requires_review',
                    'payload' => $payload,
                    'signature_valid' => false,
                    'processed_at' => now(),
                    'response_status' => 200,
                ]
            );

            return response()->json([
                'ok' => true,
                'provider' => 'clip',
                'registered' => true,
                'manual_review' => true,
            ]);
        }

        $existing = PaymentWebhookLog::where('payload_hash', $hash)
            ->orWhere(fn ($query) => $extracted['event_id'] ? $query->where('event_id', $extracted['event_id']) : $query->whereRaw('1 = 0'))
            ->first();

        if ($existing) {
            return response()->json(['ok' => true, 'duplicate' => true]);
        }

        $payment = $this->findPayment($extracted);

        $order = $payment?->order;

        $log = PaymentWebhookLog::create([
            'provider' => 'clip',
            'event_id' => $extracted['event_id'],
            'payload_hash' => $hash,
            'order_id' => $order?->id,
            'payment_request_id' => $extracted['payment_request_id'],
            'external_reference' => $extracted['external_reference'],
            'status' => $extracted['status'],
            'payload' => $payload,
            'signature_valid' => true,
        ]);

        if (! $order || ! $payment) {
            $log->update(['processed_at' => now()]);

            return response()->json(['ok' => true, 'registered' => true]);
        }

        $this->processPayment($clip, $orders, $payment, $order, $extracted, $payload, $log);

        return response()->json(['ok' => true]);
    }

    private function isDiagnosticPing(ClipService $clip, array $payload, array $extracted): bool
    {
        $status = strtolower((string) ($extracted['status'] ?? $payload['type'] ?? $payload['event'] ?? ''));
        $hasPaymentReference = $extracted['payment_request_id']
            || $extracted['external_reference']
            || $extracted['amount'] !== null
            || $extracted['currency'];

        if ($hasPaymentReference) {
            return false;
        }

        return $payload === [] || $clip->isDiagnosticStatus($status);
    }

    private function findPayment(array $extracted): ?Payment
    {
        if (! $extracted['payment_request_id'] && ! $extracted['external_reference']) {
            return null;
        }

        return Payment::query()
            ->when($extracted['payment_request_id'], fn ($query) => $query->orWhere('payment_request_id', $extracted['payment_request_id']))
            ->when($extracted['external_reference'], fn ($query) => $query->orWhere('external_reference', $extracted['external_reference']))
            ->first();
    }

    private function mergeExtracted(array $received, array $verified): array
    {
        foreach ($verified as $key => $value) {
            if ($value !== null && $value !== '') {
                $received[$key] = $value;
            }
        }

        return $received;
    }

    private function matchesPayment(Payment $payment, array $extracted): bool
    {
        if ($extracted['payment_request_id'] && $payment->payment_request_id !== $extracted['payment_request_id']) {
            return false;
        }

        if ($extracted['external_reference'] && $payment->external_reference !== $extracted['external_reference']) {
            return false;
        }

        if ($extracted['amount'] !== null && round((float) $payment->amount, 2) !== round($extracted['amount'], 2)) {
            return false;
        }

        return ! $extracted['currency'] || strtoupper($extracted['currency']) === 'MXN';
    }

    private function processPayment(ClipService $clip, OrderService $orders, Payment $payment, Order $order, array $extracted, array $payload, PaymentWebhookLog $log): void
    {
        if ($extracted['amount'] !== null && round((float) $payment->amount, 2) !== round($extracted['amount'], 2)) {
            $log->update(['status' => 'amount_mismatch', 'processed_at' => now()]);

            return;
        }

        if ($extracted['currency'] && strtoupper($extracted['currency']) !== 'MXN') {
            $log->update(['status' => 'currency_mismatch', 'processed_at' => now()]);

            return;
        }

        if ($clip->isPaidStatus($extracted['status'])) {
            $orders->markAsPaid($order, [
                'receipt_no' => $extracted['receipt_no'],
                'transaction_id' => $extracted['transaction_id'],
                'raw_response' => $payload,
            ]);
        } elseif ($clip->isFailedStatus($extracted['status'])) {
            $status = str_contains(strtolower((string) $extracted['status']), 'expir') ? Order::STATUS_EXPIRED : Order::STATUS_CANCELLED;
            $orders->transition($order, $status);
        }

        $log->update(['processed_at' => now()]);
    }
}
