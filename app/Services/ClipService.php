<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use RuntimeException;

class ClipService
{
    public function createCheckout(Order $order): array
    {
        $description = Str::limit('Pedido '.$order->folio.' en '.config('app.name'), 127, '');
        $webhookUrl = config('services.clip.webhook_url') ?: route('webhooks.clip');
        $receivedUrl = URL::signedRoute('checkout.received', $order);
        $returnUrl = URL::signedRoute('checkout.clip.return', ['order' => $order, 'folio' => $order->folio]);
        $cancelledUrl = URL::signedRoute('checkout.clip.cancelled', ['order' => $order, 'folio' => $order->folio]);

        $payload = [
            'amount' => (int) round((float) $order->total),
            'currency' => 'MXN',
            'purchase_description' => $description,
            'redirection_url' => [
                'success' => $this->returnUrl('success_url', $returnUrl, $order->folio),
                'error' => $this->returnUrl('error_url', $cancelledUrl, $order->folio),
                'default' => $receivedUrl,
            ],
            'override_settings' => [
                'locale' => 'es-MX',
                'tip_enabled' => false,
                'merchant_redirect_url' => $receivedUrl,
            ],
            'metadata' => [
                'external_reference' => $order->folio,
                'order_id' => (string) $order->id,
                'merch_inv_id' => $order->folio,
                'customer_info' => [
                    'name' => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                ],
            ],
            'webhook_url' => $webhookUrl,
        ];

        $response = $this->client()->post('/v2/checkout', $payload);

        if ($response->failed() && str_contains($response->body(), 'BR1801')) {
            unset($payload['metadata']['customer_info']);
            $response = $this->client()->post('/v2/checkout', $payload);
        }

        $this->throwIfFailed($response);

        $json = $response->json();

        return array_merge($json, [
            'payment_request_url' => $json['payment_request_url'] ?? $json['checkout_url'] ?? $json['url'] ?? null,
            'payment_request_id' => $json['payment_request_id'] ?? $json['id'] ?? $json['payment_request']['id'] ?? null,
        ]);
    }

    public function status(string $paymentRequestId): array
    {
        $response = $this->client()->get('/v2/checkout/'.$paymentRequestId);
        $this->throwIfFailed($response);

        return $response->json();
    }

    public function validateSignature(Request $request): bool
    {
        $secret = config('services.clip.webhook_secret');

        if (! $secret) {
            // Sin secreto configurado se rechaza por defecto. Solo se acepta si
            // se habilita explicitamente el opt-in de pruebas y no es produccion.
            return config('services.clip.allow_unsigned_webhook', false)
                && ! app()->environment('production');
        }

        $signature = $request->headers->get('x-clip-signature')
            ?: $request->headers->get('clip-signature')
            ?: $request->headers->get('x-signature');

        if (! $signature) {
            return false;
        }

        $expected = hash_hmac('sha256', $request->getContent(), $secret);
        $signature = Str::after($signature, 'sha256=');

        return hash_equals($expected, $signature);
    }

    public function extract(array $payload): array
    {
        $data = $payload['data'] ?? $payload;
        $item = $data['item'] ?? $payload['item'] ?? [];
        $meta = $data['meta'] ?? $payload['meta'] ?? [];
        $paymentDetail = $data['payment_detail'] ?? $payload['payment_detail'] ?? [];
        $requestDetail = $data['payment_request_detail'] ?? $payload['payment_request_detail'] ?? [];
        $metadata = $data['metadata'] ?? $payload['metadata'] ?? $item['metadata'] ?? $paymentDetail['metadata'] ?? $requestDetail['metadata'] ?? [];
        $eventType = $payload['event_type'] ?? $data['event_type'] ?? null;
        $status = $data['status']
            ?? $payload['status']
            ?? $item['status']
            ?? $data['resource_status']
            ?? $payload['resource_status']
            ?? $paymentDetail['status']
            ?? $requestDetail['status']
            ?? $data['payment_status']
            ?? $meta['item_type']
            ?? $eventType;

        return [
            'event_id' => $payload['id'] ?? $payload['event_id'] ?? $data['id'] ?? $meta['event_id'] ?? null,
            'payment_request_id' => $data['payment_request_id'] ?? $payload['payment_request_id'] ?? $item['payment_request_id'] ?? $data['payment_request']['id'] ?? $requestDetail['id'] ?? $data['payment_request_code'] ?? $payload['payment_request_code'] ?? $item['payment_request_code'] ?? null,
            'external_reference' => $metadata['external_reference'] ?? $metadata['merch_inv_id'] ?? $data['external_reference'] ?? $payload['external_reference'] ?? $item['external_reference'] ?? $data['me_reference_id'] ?? $payload['me_reference_id'] ?? $item['me_reference_id'] ?? $data['merch_inv_id'] ?? $payload['merch_inv_id'] ?? $item['merch_inv_id'] ?? $data['merchant_invoice_id'] ?? $payload['merchant_invoice_id'] ?? $item['merchant_invoice_id'] ?? $data['merchant_invoice'] ?? $payload['merchant_invoice'] ?? $item['merchant_invoice'] ?? $paymentDetail['merch_inv_id'] ?? $requestDetail['merch_inv_id'] ?? null,
            'status' => $status,
            'amount' => $this->amountFrom($data, $payload, $item, $paymentDetail, $requestDetail),
            'currency' => $data['currency'] ?? $payload['currency'] ?? $item['currency'] ?? $paymentDetail['currency'] ?? $requestDetail['currency'] ?? null,
            'receipt_no' => $data['receipt_no'] ?? $payload['receipt_no'] ?? $item['receipt_no'] ?? $paymentDetail['receipt_no'] ?? null,
            'transaction_id' => $data['transaction_id'] ?? $data['transaction']['id'] ?? $item['transaction_id'] ?? $item['transaction']['id'] ?? $paymentDetail['transaction_id'] ?? $paymentDetail['transaction']['id'] ?? $payload['transaction_id'] ?? $payload['transaction']['id'] ?? null,
        ];
    }

    public function testConnection(array $override = []): array
    {
        $payloads = [
            [
                'amount' => 1,
                'currency' => 'MXN',
                'purchase_description' => 'Prueba de conexion '.config('app.name'),
                'metadata' => ['external_reference' => 'TEST-'.now()->format('YmdHis')],
            ],
            [
                'amount' => 1,
                'purchase_description' => 'Prueba de conexion '.config('app.name'),
            ],
        ];

        $last = null;

        foreach ($payloads as $index => $payload) {
            try {
                $response = $this->client($override)->post('/v2/checkout', $payload);
                $last = [
                    'ok' => $response->successful(),
                    'status' => $response->status(),
                    'test' => $index + 1,
                    'message' => $response->successful()
                        ? 'Conexion correcta con Clip. Las credenciales respondieron bien.'
                        : 'Clip respondio '.$response->status().': '.Str::limit($response->body(), 180),
                ];

                if ($response->successful() || $response->status() !== 500) {
                    return $last;
                }
            } catch (RuntimeException $exception) {
                return [
                    'ok' => false,
                    'status' => 422,
                    'message' => $exception->getMessage(),
                ];
            } catch (\Throwable $exception) {
                return [
                    'ok' => false,
                    'status' => 500,
                    'message' => 'No se pudo conectar con Clip: '.$exception->getMessage(),
                ];
            }
        }

        return $last ?: [
            'ok' => false,
            'status' => 422,
            'message' => 'No fue posible comprobar Clip.',
        ];
    }

    public function isPaidStatus(?string $status): bool
    {
        return in_array(Str::lower((string) $status), [
            'paid',
            'completed',
            'approved',
            'success',
            'successful',
            'payment_approved',
            'approved_payment',
            'payment.approved',
            'charge.succeeded',
            'charge_success',
            'payment_completed',
            'checkout_completed',
            'request_completed',
        ], true);
    }

    public function isDiagnosticStatus(?string $status): bool
    {
        $status = Str::lower(trim((string) $status));

        return $status === ''
            || in_array($status, [
                'test',
                'ping',
                'insert',
                'update',
                'webhook_test',
                'webhook.test',
                'verification',
            ], true)
            || str_contains($status, 'test')
            || str_contains($status, 'ping');
    }

    public function isFailedStatus(?string $status): bool
    {
        return in_array(Str::lower((string) $status), [
            'failed',
            'declined',
            'cancelled',
            'canceled',
            'expired',
            'rejected',
            'payment_rejected',
            'payment_declined',
            'payment_cancelled',
            'payment_canceled',
            'payment_expired',
            'payment.failed',
            'payment.declined',
            'payment.cancelled',
            'payment.expired',
            'payment_failed',
            'request_cancelled',
            'request_expired',
            'request_failed',
        ], true);
    }

    private function client(array $override = []): \Illuminate\Http\Client\PendingRequest
    {
        $authorization = $this->authorization($override);

        return Http::baseUrl(rtrim($override['base_url'] ?? config('services.clip.base_url'), '/'))
            ->timeout(20)
            ->acceptJson()
            ->asJson()
            ->withHeaders(['Authorization' => $authorization]);
    }

    private function authorization(array $override = []): string
    {
        $publicKey = $override['public_key'] ?? config('services.clip.public_key');
        $secretKey = $override['secret_key'] ?? config('services.clip.secret_key');
        $apiKey = $override['api_key'] ?? config('services.clip.api_key');
        $scheme = $override['auth_scheme'] ?? config('services.clip.auth_scheme', 'Basic');

        if ($publicKey && $secretKey) {
            return 'Basic '.base64_encode($publicKey.':'.$secretKey);
        }

        if (! $apiKey) {
            throw new RuntimeException('Configura CLIP_PUBLIC_KEY y CLIP_SECRET_KEY, o CLIP_API_KEY si usas token legacy.');
        }

        return preg_match('/^(Basic|Bearer)\s+/i', $apiKey)
            ? $apiKey
            : trim($scheme.' '.$apiKey);
    }

    private function returnUrl(string $configKey, string $fallback, string $folio): string
    {
        $url = config('services.clip.'.$configKey);

        if (! $url) {
            return $fallback;
        }

        $separator = str_contains($url, '?') ? '&' : '?';

        return $url.$separator.'folio='.urlencode($folio);
    }

    private function throwIfFailed(Response $response): void
    {
        if ($response->failed()) {
            throw new RuntimeException('Clip rechazo la solicitud: '.$response->status().' '.$response->body());
        }
    }

    private function amountFrom(array ...$sources): ?float
    {
        foreach ($sources as $source) {
            foreach (['amount', 'total', 'paid_amount'] as $key) {
                if (isset($source[$key])) {
                    return (float) $source[$key];
                }
            }
        }

        return null;
    }
}
