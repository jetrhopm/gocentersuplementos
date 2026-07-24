<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class MetaAdsService
{
    private const GRAPH_VERSION = 'v25.0';

    public function enabled(): bool
    {
        return (bool) (config('services.marketing.meta_enabled') && $this->pixelId());
    }

    public function browserEvent(string $eventName, array $customData = [], ?string $eventId = null): ?array
    {
        if (! $this->enabled()) {
            return null;
        }

        return [
            'name' => $eventName,
            'event_id' => $eventId ?: $this->eventId($eventName),
            'custom_data' => $this->cleanCustomData($customData),
        ];
    }

    public function productPayload(Product $product, int $quantity = 1): array
    {
        $id = $this->productId($product);
        $price = round((float) $product->price, 2);

        return [
            'currency' => 'MXN',
            'value' => round($price * max(1, $quantity), 2),
            'content_ids' => [$id],
            'content_name' => $product->name,
            'content_type' => 'product',
            'contents' => [[
                'id' => $id,
                'quantity' => max(1, $quantity),
                'item_price' => $price,
            ]],
        ];
    }

    public function cartPayload(Collection $items, ?array $totals = null): array
    {
        $contents = $items->map(function (array $item) {
            $product = $item['product'];

            return [
                'id' => $this->productId($product),
                'quantity' => (int) $item['quantity'],
                'item_price' => round((float) $item['unit_price'], 2),
            ];
        })->values()->all();

        return [
            'currency' => 'MXN',
            'value' => round((float) ($totals['total'] ?? $items->sum('total')), 2),
            'content_ids' => collect($contents)->pluck('id')->all(),
            'content_type' => 'product',
            'contents' => $contents,
            'num_items' => (int) $items->sum('quantity'),
        ];
    }

    public function orderPayload(Order $order): array
    {
        $order->loadMissing('items');

        return [
            'currency' => 'MXN',
            'value' => round((float) $order->total, 2),
            'content_ids' => $order->items->map(fn ($item) => (string) ($item->product_id ?: Str::slug($item->product_name)))->values()->all(),
            'content_type' => 'product',
            'contents' => $order->items->map(fn ($item) => [
                'id' => (string) ($item->product_id ?: Str::slug($item->product_name)),
                'quantity' => (int) $item->quantity,
                'item_price' => round((float) $item->unit_price, 2),
            ])->values()->all(),
            'num_items' => (int) $order->items->sum('quantity'),
            'order_id' => $order->folio,
        ];
    }

    public function sendPurchase(Order $order, ?Request $request = null, ?string $eventId = null): void
    {
        $this->sendEvent(
            'Purchase',
            $this->orderPayload($order),
            $request,
            $eventId ?: $this->purchaseEventId($order),
            $this->customerDataFromOrder($order)
        );
    }

    public function sendEvent(
        string $eventName,
        array $customData = [],
        ?Request $request = null,
        ?string $eventId = null,
        array $customerData = []
    ): void {
        if (! $this->enabled() || ! $this->accessToken()) {
            return;
        }

        try {
            $event = [
                'event_name' => $eventName,
                'event_time' => time(),
                'action_source' => 'website',
                'event_id' => $eventId ?: $this->eventId($eventName),
                'custom_data' => $this->cleanCustomData($customData),
                'user_data' => $this->userData($request, $customerData),
            ];

            if ($request) {
                $event['event_source_url'] = $request->fullUrl();
            }

            $payload = ['data' => [$event]];

            if ($testCode = config('services.marketing.meta_test_event_code')) {
                $payload['test_event_code'] = $testCode;
            }

            $response = Http::timeout(8)
                ->acceptJson()
                ->asJson()
                ->post($this->endpoint($this->pixelId(), $this->accessToken()), $payload);

            if (! $response->successful()) {
                Log::warning('Meta CAPI event rejected.', [
                    'event' => $eventName,
                    'status' => $response->status(),
                    'body' => Str::limit($response->body(), 800),
                ]);
            }
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function testConnection(array $override = []): array
    {
        $pixelId = trim((string) ($override['pixel_id'] ?? $this->pixelId()));
        $token = trim((string) ($override['access_token'] ?? $this->accessToken()));

        if ($pixelId === '' || $token === '') {
            return [
                'ok' => false,
                'message' => 'Captura Meta Pixel ID y Conversions API access token para probar la conexion.',
            ];
        }

        try {
            $payload = [
                'data' => [[
                    'event_name' => 'TestEvent',
                    'event_time' => time(),
                    'action_source' => 'website',
                    'event_id' => $this->eventId('meta_test'),
                    'event_source_url' => (string) config('app.url'),
                    'user_data' => [
                        'client_user_agent' => 'GoCenterMetaTest/1.0',
                    ],
                    'custom_data' => [
                        'currency' => 'MXN',
                        'value' => 0,
                    ],
                ]],
            ];

            if (! empty($override['test_event_code'])) {
                $payload['test_event_code'] = trim((string) $override['test_event_code']);
            }

            $response = Http::timeout(8)
                ->acceptJson()
                ->asJson()
                ->post($this->endpoint($pixelId, $token), $payload);

            if ($response->successful()) {
                return [
                    'ok' => true,
                    'message' => 'Conexion correcta con Meta. Revisa el evento de prueba en Events Manager.',
                ];
            }

            $message = $this->metaErrorMessage($response->json()) ?: 'Revisa Pixel ID y token CAPI.';

            return [
                'ok' => false,
                'message' => 'Meta rechazo la prueba: '.$this->friendlyMetaError($message),
            ];
        } catch (Throwable $exception) {
            report($exception);

            return [
                'ok' => false,
                'message' => 'No se pudo conectar con Meta. Revisa internet, Pixel ID y token CAPI.',
            ];
        }
    }

    public function purchaseEventId(Order $order): string
    {
        return 'purchase_'.$order->folio;
    }

    public function eventId(string $prefix): string
    {
        return Str::slug($prefix, '_').'_'.Str::uuid();
    }

    private function productId(Product $product): string
    {
        return (string) ($product->sku ?: $product->slug ?: $product->id);
    }

    private function userData(?Request $request, array $customerData): array
    {
        $data = [];

        if ($email = $this->hash($customerData['email'] ?? null)) {
            $data['em'] = [$email];
        }

        if ($phone = $this->hashPhone($customerData['phone'] ?? null)) {
            $data['ph'] = [$phone];
        }

        if ($externalId = $this->hash($customerData['external_id'] ?? null)) {
            $data['external_id'] = [$externalId];
        }

        if ($request) {
            $data['client_ip_address'] = $request->ip();
            $data['client_user_agent'] = (string) $request->userAgent();

            if ($request->cookie('_fbp')) {
                $data['fbp'] = $request->cookie('_fbp');
            }

            if ($request->cookie('_fbc')) {
                $data['fbc'] = $request->cookie('_fbc');
            }
        }

        return array_filter($data, fn ($value) => $value !== null && $value !== '' && $value !== []);
    }

    private function customerDataFromOrder(Order $order): array
    {
        return [
            'email' => $order->customer_email,
            'phone' => $order->customer_phone,
            'external_id' => $order->folio,
        ];
    }

    private function cleanCustomData(array $data): array
    {
        return collect($data)
            ->filter(fn ($value) => $value !== null && $value !== '' && $value !== [])
            ->all();
    }

    private function hash(?string $value): ?string
    {
        $value = trim(strtolower((string) $value));

        return $value === '' ? null : hash('sha256', $value);
    }

    private function hashPhone(?string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);

        return $digits ? hash('sha256', $digits) : null;
    }

    private function pixelId(): ?string
    {
        return filled(config('services.marketing.meta_pixel_id')) ? (string) config('services.marketing.meta_pixel_id') : null;
    }

    private function accessToken(): ?string
    {
        return filled(config('services.marketing.meta_capi_access_token')) ? (string) config('services.marketing.meta_capi_access_token') : null;
    }

    private function endpoint(?string $pixelId = null, ?string $accessToken = null): string
    {
        $url = 'https://graph.facebook.com/'.self::GRAPH_VERSION.'/'.($pixelId ?: $this->pixelId()).'/events';
        $token = $accessToken ?: $this->accessToken();

        return $token ? $url.'?access_token='.urlencode($token) : $url;
    }

    private function metaErrorMessage(mixed $response): string
    {
        if (! is_array($response)) {
            return '';
        }

        return (string) data_get($response, 'error.message', '');
    }

    private function friendlyMetaError(string $message): string
    {
        if (str_contains(strtolower($message), 'bad signature')) {
            return 'Bad signature. El Pixel puede estar activo, pero el token CAPI no fue aceptado por Meta. Revisa que sea el token de Conversions API generado para este Pixel ID, sin espacios ni texto extra.';
        }

        return $message;
    }
}
