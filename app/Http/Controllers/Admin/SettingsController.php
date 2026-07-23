<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminSettingsRequest;
use App\Mail\TestMail;
use App\Services\ClipService;
use App\Services\EnvFileService;
use App\Services\MetaAdsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SettingsController extends Controller
{
    public function index(EnvFileService $env)
    {
        $keys = $this->keys();
        $values = $env->values($keys);

        return view('admin.settings.index', [
            'values' => $values,
            'masked' => [
                'CLIP_PUBLIC_KEY' => $env->masked($values['CLIP_PUBLIC_KEY'] ?? null),
                'CLIP_SECRET_KEY' => $env->masked($values['CLIP_SECRET_KEY'] ?? null),
                'CLIP_API_KEY' => $env->masked($values['CLIP_API_KEY'] ?? null),
                'CLIP_WEBHOOK_SECRET' => $env->masked($values['CLIP_WEBHOOK_SECRET'] ?? null),
                'META_CAPI_ACCESS_TOKEN' => $env->masked($values['META_CAPI_ACCESS_TOKEN'] ?? null),
                'MAIL_PASSWORD' => $env->masked($values['MAIL_PASSWORD'] ?? null),
            ],
        ]);
    }

    public function update(AdminSettingsRequest $request, EnvFileService $env)
    {
        $data = $request->validated();

        $clearMetaCapiToken = (bool) ($data['META_CAPI_ACCESS_TOKEN_CLEAR'] ?? false);
        unset($data['META_CAPI_ACCESS_TOKEN_CLEAR']);

        foreach (['CLIP_PUBLIC_KEY', 'CLIP_SECRET_KEY', 'CLIP_API_KEY', 'CLIP_WEBHOOK_SECRET', 'MAIL_PASSWORD'] as $secretKey) {
            if (($data[$secretKey] ?? '') === '') {
                unset($data[$secretKey]);
            }
        }

        if ($clearMetaCapiToken) {
            $data['META_CAPI_ACCESS_TOKEN'] = '';
        } elseif (($data['META_CAPI_ACCESS_TOKEN'] ?? '') === '') {
            unset($data['META_CAPI_ACCESS_TOKEN']);
        }

        $env->update($data);

        return redirect()->route('admin.settings.index')->with('status', 'Configuracion guardada. Se limpio cache de rutas y config.');
    }

    public function testClip(Request $request, EnvFileService $env, ClipService $clip)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $values = $env->values($this->keys());

        $override = [
            'base_url' => $request->input('CLIP_BASE_URL') ?: ($values['CLIP_BASE_URL'] ?? null),
            'auth_scheme' => $request->input('CLIP_AUTH_SCHEME') ?: ($values['CLIP_AUTH_SCHEME'] ?? null),
            'public_key' => $request->filled('CLIP_PUBLIC_KEY') ? $request->input('CLIP_PUBLIC_KEY') : ($values['CLIP_PUBLIC_KEY'] ?? null),
            'secret_key' => $request->filled('CLIP_SECRET_KEY') ? $request->input('CLIP_SECRET_KEY') : ($values['CLIP_SECRET_KEY'] ?? null),
            'api_key' => $request->filled('CLIP_API_KEY') ? $request->input('CLIP_API_KEY') : ($values['CLIP_API_KEY'] ?? null),
            'webhook_url' => $request->input('CLIP_WEBHOOK_URL') ?: ($values['CLIP_WEBHOOK_URL'] ?? null),
            'success_url' => $request->input('CLIP_SUCCESS_URL') ?: ($values['CLIP_SUCCESS_URL'] ?? null),
            'error_url' => $request->input('CLIP_ERROR_URL') ?: ($values['CLIP_ERROR_URL'] ?? null),
        ];

        $result = $clip->testConnection($override);

        return response()->json($result, $result['ok'] ? 200 : 422);
    }

    public function testMail(Request $request, EnvFileService $env)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'MAIL_MAILER' => ['required', 'in:log,smtp,array'],
            'MAIL_SCHEME' => ['nullable', 'string', 'max:20'],
            'MAIL_HOST' => ['nullable', 'string', 'max:160'],
            'MAIL_PORT' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'MAIL_USERNAME' => ['nullable', 'string', 'max:180'],
            'MAIL_PASSWORD' => ['nullable', 'string', 'max:1000'],
            'MAIL_FROM_ADDRESS' => ['required', 'email', 'max:180'],
            'MAIL_FROM_NAME' => ['required', 'string', 'max:120'],
            'test_email' => ['required', 'email', 'max:180'],
        ]);

        $values = $env->values($this->keys());
        $password = $request->filled('MAIL_PASSWORD')
            ? $data['MAIL_PASSWORD']
            : ($values['MAIL_PASSWORD'] ?? null);
        $scheme = $this->normalizeMailScheme($data['MAIL_SCHEME'] ?? null);

        if (! in_array($scheme, [null, 'smtp', 'smtps'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'Scheme no valido. Usa smtps para puerto 465 o smtp para puerto 587.',
            ], 422);
        }

        config([
            'mail.default' => $data['MAIL_MAILER'],
            'mail.from.address' => $data['MAIL_FROM_ADDRESS'],
            'mail.from.name' => $data['MAIL_FROM_NAME'],
            'mail.mailers.smtp.scheme' => $scheme,
            'mail.mailers.smtp.host' => $data['MAIL_HOST'] ?: null,
            'mail.mailers.smtp.port' => $data['MAIL_PORT'] ?: null,
            'mail.mailers.smtp.username' => $data['MAIL_USERNAME'] ?: null,
            'mail.mailers.smtp.password' => $password ?: null,
        ]);

        app('mail.manager')->forgetMailers();

        try {
            Mail::to($data['test_email'])->send(new TestMail(
                $data['MAIL_FROM_NAME'],
                $data['MAIL_FROM_ADDRESS']
            ));
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'ok' => false,
                'message' => 'No se pudo enviar el correo. Revisa host, puerto, scheme, usuario y password.',
            ], 422);
        }

        $message = $data['MAIL_MAILER'] === 'smtp'
            ? 'Correo de prueba enviado a '.$data['test_email'].'.'
            : 'Prueba correcta. El mailer '.$data['MAIL_MAILER'].' no envia correos reales por SMTP.';

        return response()->json([
            'ok' => true,
            'message' => $message,
        ]);
    }

    public function testMeta(Request $request, EnvFileService $env, MetaAdsService $metaAds)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $values = $env->values($this->keys());

        $data = $request->validate([
            'META_PIXEL_ID' => ['nullable', 'string', 'max:60', 'regex:/^[0-9]+$/'],
            'META_CAPI_ACCESS_TOKEN' => ['nullable', 'string', 'max:2000'],
            'META_TEST_EVENT_CODE' => ['nullable', 'string', 'max:120'],
            'META_CAPI_ACCESS_TOKEN_CLEAR' => ['nullable', 'boolean'],
        ]);

        $result = $metaAds->testConnection([
            'pixel_id' => $data['META_PIXEL_ID'] ?: ($values['META_PIXEL_ID'] ?? null),
            'access_token' => $request->boolean('META_CAPI_ACCESS_TOKEN_CLEAR')
                ? null
                : ($request->filled('META_CAPI_ACCESS_TOKEN')
                ? $data['META_CAPI_ACCESS_TOKEN']
                : ($values['META_CAPI_ACCESS_TOKEN'] ?? null)),
            'test_event_code' => $data['META_TEST_EVENT_CODE'] ?: ($values['META_TEST_EVENT_CODE'] ?? null),
        ]);

        return response()->json($result, $result['ok'] ? 200 : 422);
    }

    private function normalizeMailScheme(mixed $scheme): ?string
    {
        $scheme = strtolower(trim((string) $scheme));

        return match ($scheme) {
            '', 'null', 'none' => null,
            'ssl', 'smtps' => 'smtps',
            'tls', 'starttls', 'smtp' => 'smtp',
            default => $scheme,
        };
    }

    private function keys(): array
    {
        return [
            'APP_NAME',
            'APP_URL',
            'STORE_META_DESCRIPTION',
            'STORE_WHATSAPP',
            'STORE_THEME',
            'STORE_HEADER_SHOW_TITLE',
            'STORE_SHIPPING_COST',
            'STORE_FREE_SHIPPING_FROM',
            'STORE_LOW_STOCK_THRESHOLD',
            'STORE_MAX_UPLOAD_KB',
            'STORE_ADMIN_ORDER_EMAILS',
            'STORE_MAINTENANCE_MODE',
            'STORE_HERO_CAROUSEL_SLUGS',
            'STORE_PRODUCT_CAROUSEL_SLUGS',
            'BANK_NAME',
            'BANK_ACCOUNT_HOLDER',
            'BANK_ACCOUNT_NUMBER',
            'BANK_CLABE',
            'BANK_TRANSFER_INSTRUCTIONS',
            'OXXO_PAYMENT_QR_PATH',
            'OXXO_PAYMENT_REFERENCE',
            'OXXO_PAYMENT_INSTRUCTIONS',
            'CLIP_BASE_URL',
            'CLIP_AUTH_SCHEME',
            'CLIP_PUBLIC_KEY',
            'CLIP_SECRET_KEY',
            'CLIP_API_KEY',
            'CLIP_WEBHOOK_SECRET',
            'CLIP_WEBHOOK_URL',
            'CLIP_SUCCESS_URL',
            'CLIP_ERROR_URL',
            'META_ADS_ENABLED',
            'META_PIXEL_ID',
            'META_CAPI_ACCESS_TOKEN',
            'META_TEST_EVENT_CODE',
            'GOOGLE_SEARCH_ENABLED',
            'GOOGLE_SITE_VERIFICATION',
            'GOOGLE_ADS_ENABLED',
            'GOOGLE_TAG_ID',
            'GOOGLE_ADS_CONVERSION_ID',
            'GOOGLE_ADS_CONVERSION_LABEL',
            'MAIL_MAILER',
            'MAIL_SCHEME',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'MAIL_FROM_ADDRESS',
            'MAIL_FROM_NAME',
        ];
    }
}
