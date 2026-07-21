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
            if ($this->isDiagnosticPing($payload, $extracted)) {
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

            PaymentWebhookLog::firstOrCreate(
                ['payload_hash' => $hash],
                [
                    'provider' => 'clip',
                    'event_id' => $extracted['event_id'],
                    'payment_request_id' => $extracted['payment_request_id'],
                    'external_reference' => $extracted['external_reference'],
                    'status' => 'invalid_signature',
                    'payload' => $payload,
                    'signature_valid' => false,
                    'processed_at' => now(),
                    'response_status' => 401,
                ]
            );

            return response()->json(['ok' => false], 401);
        }

        $existing = PaymentWebhookLog::where('payload_hash', $hash)
            ->orWhere(fn ($query) => $extracted['event_id'] ? $query->where('event_id', $extracted['event_id']) : $query->whereRaw('1 = 0'))
            ->first();

        if ($existing) {
            return response()->json(['ok' => true, 'duplicate' => true]);
        }

        $payment = null;

        if ($extracted['payment_request_id'] || $extracted['external_reference']) {
            $payment = Payment::query()
                ->when($extracted['payment_request_id'], fn ($query) => $query->orWhere('payment_request_id', $extracted['payment_request_id']))
                ->when($extracted['external_reference'], fn ($query) => $query->orWhere('external_reference', $extracted['external_reference']))
                ->first();
        }

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

        if ($extracted['amount'] !== null && round((float) $payment->amount, 2) !== round($extracted['amount'], 2)) {
            $log->update(['status' => 'amount_mismatch', 'processed_at' => now()]);

            return response()->json(['ok' => true, 'registered' => true]);
        }

        if ($extracted['currency'] && strtoupper($extracted['currency']) !== 'MXN') {
            $log->update(['status' => 'currency_mismatch', 'processed_at' => now()]);

            return response()->json(['ok' => true, 'registered' => true]);
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

        return response()->json(['ok' => true]);
    }

    private function isDiagnosticPing(array $payload, array $extracted): bool
    {
        $status = strtolower((string) ($extracted['status'] ?? $payload['type'] ?? $payload['event'] ?? ''));
        $hasPaymentReference = $extracted['payment_request_id']
            || $extracted['external_reference']
            || $extracted['amount'] !== null
            || $extracted['currency'];

        if ($hasPaymentReference) {
            return false;
        }

        return $payload === []
            || in_array($status, ['test', 'ping', 'webhook_test', 'webhook.test', 'verification'], true)
            || str_contains($status, 'test')
            || str_contains($status, 'ping');
    }
}
