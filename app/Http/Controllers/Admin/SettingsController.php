<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminSettingsRequest;
use App\Services\ClipService;
use App\Services\EnvFileService;
use Illuminate\Http\Request;

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
                'MAIL_PASSWORD' => $env->masked($values['MAIL_PASSWORD'] ?? null),
            ],
        ]);
    }

    public function update(AdminSettingsRequest $request, EnvFileService $env)
    {
        $data = $request->validated();

        foreach (['CLIP_PUBLIC_KEY', 'CLIP_SECRET_KEY', 'CLIP_API_KEY', 'CLIP_WEBHOOK_SECRET', 'MAIL_PASSWORD'] as $secretKey) {
            if (($data[$secretKey] ?? '') === '') {
                unset($data[$secretKey]);
            }
        }

        $env->update($data);

        return redirect()->route('admin.settings.index')->with('status', 'Configuracion guardada. Se limpio cache de rutas y config.');
    }

    public function testClip(Request $request, EnvFileService $env, ClipService $clip)
    {
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

    private function keys(): array
    {
        return [
            'APP_NAME',
            'APP_URL',
            'STORE_META_DESCRIPTION',
            'STORE_WHATSAPP',
            'STORE_THEME',
            'STORE_SHIPPING_COST',
            'STORE_FREE_SHIPPING_FROM',
            'STORE_LOW_STOCK_THRESHOLD',
            'STORE_MAX_UPLOAD_KB',
            'STORE_MAINTENANCE_MODE',
            'STORE_HERO_CAROUSEL_SLUGS',
            'STORE_PRODUCT_CAROUSEL_SLUGS',
            'BANK_NAME',
            'BANK_ACCOUNT_HOLDER',
            'BANK_ACCOUNT_NUMBER',
            'BANK_CLABE',
            'BANK_TRANSFER_INSTRUCTIONS',
            'CLIP_BASE_URL',
            'CLIP_AUTH_SCHEME',
            'CLIP_PUBLIC_KEY',
            'CLIP_SECRET_KEY',
            'CLIP_API_KEY',
            'CLIP_WEBHOOK_SECRET',
            'CLIP_WEBHOOK_URL',
            'CLIP_SUCCESS_URL',
            'CLIP_ERROR_URL',
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
