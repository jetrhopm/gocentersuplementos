<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'STORE_MAINTENANCE_MODE' => $this->boolean('STORE_MAINTENANCE_MODE'),
            'STORE_HEADER_SHOW_TITLE' => $this->boolean('STORE_HEADER_SHOW_TITLE'),
            'META_ADS_ENABLED' => $this->boolean('META_ADS_ENABLED'),
            'GOOGLE_SEARCH_ENABLED' => $this->boolean('GOOGLE_SEARCH_ENABLED'),
            'GOOGLE_ADS_ENABLED' => $this->boolean('GOOGLE_ADS_ENABLED'),
            'MAIL_SCHEME' => $this->normalizeMailScheme($this->input('MAIL_SCHEME')),
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'APP_NAME' => ['required', 'string', 'max:80'],
            'APP_URL' => ['required', 'url', 'max:180'],
            'STORE_META_DESCRIPTION' => ['nullable', 'string', 'max:220'],
            'STORE_WHATSAPP' => ['nullable', 'string', 'max:30'],
            'STORE_THEME' => ['required', Rule::in(['volt', 'ember', 'glacier', 'gocenter'])],
            'STORE_HEADER_SHOW_TITLE' => ['boolean'],
            'STORE_SHIPPING_COST' => ['required', 'numeric', 'min:0', 'max:99999'],
            'STORE_FREE_SHIPPING_FROM' => ['required', 'numeric', 'min:0', 'max:999999'],
            'STORE_LOW_STOCK_THRESHOLD' => ['required', 'integer', 'min:0', 'max:9999'],
            'STORE_MAX_UPLOAD_KB' => ['required', 'integer', 'min:512', 'max:10240'],
            'STORE_MAINTENANCE_MODE' => ['boolean'],
            'STORE_HERO_CAROUSEL_SLUGS' => ['nullable', 'string', 'max:1000'],
            'STORE_PRODUCT_CAROUSEL_SLUGS' => ['nullable', 'string', 'max:1400'],
        ];

        if (! $this->user()?->isSuperAdmin()) {
            return $rules;
        }

        return array_merge($rules, [
            'BANK_NAME' => ['nullable', 'string', 'max:120'],
            'BANK_ACCOUNT_HOLDER' => ['nullable', 'string', 'max:160'],
            'BANK_ACCOUNT_NUMBER' => ['nullable', 'string', 'max:80'],
            'BANK_CLABE' => ['nullable', 'string', 'max:30'],
            'BANK_TRANSFER_INSTRUCTIONS' => ['nullable', 'string', 'max:500'],
            'OXXO_PAYMENT_QR_PATH' => ['nullable', 'string', 'max:220'],
            'OXXO_PAYMENT_REFERENCE' => ['nullable', 'string', 'max:80'],
            'OXXO_PAYMENT_INSTRUCTIONS' => ['nullable', 'string', 'max:700'],
            'STORE_ADMIN_ORDER_EMAILS' => ['nullable', 'string', 'max:600'],

            'CLIP_BASE_URL' => ['required', 'url', 'max:180'],
            'CLIP_AUTH_SCHEME' => ['required', Rule::in(['Bearer', 'Basic'])],
            'CLIP_PUBLIC_KEY' => ['nullable', 'string', 'max:1000'],
            'CLIP_SECRET_KEY' => ['nullable', 'string', 'max:1000'],
            'CLIP_API_KEY' => ['nullable', 'string', 'max:1000'],
            'CLIP_WEBHOOK_SECRET' => ['nullable', 'string', 'max:1000'],
            'CLIP_WEBHOOK_URL' => ['nullable', 'url', 'max:220'],
            'CLIP_SUCCESS_URL' => ['nullable', 'url', 'max:220'],
            'CLIP_ERROR_URL' => ['nullable', 'url', 'max:220'],

            'META_ADS_ENABLED' => ['boolean'],
            'META_PIXEL_ID' => ['nullable', 'string', 'max:60', 'regex:/^[0-9]+$/'],
            'META_CAPI_ACCESS_TOKEN' => ['nullable', 'string', 'max:2000'],
            'META_TEST_EVENT_CODE' => ['nullable', 'string', 'max:120'],

            'GOOGLE_SEARCH_ENABLED' => ['boolean'],
            'GOOGLE_SITE_VERIFICATION' => ['nullable', 'string', 'max:220'],
            'GOOGLE_ADS_ENABLED' => ['boolean'],
            'GOOGLE_TAG_ID' => ['nullable', 'string', 'max:80', 'regex:/^(G|AW|GT)-[A-Za-z0-9_-]+$/'],
            'GOOGLE_ADS_CONVERSION_ID' => ['nullable', 'string', 'max:80', 'regex:/^AW-[A-Za-z0-9_-]+$/'],
            'GOOGLE_ADS_CONVERSION_LABEL' => ['nullable', 'string', 'max:120'],

            'MAIL_MAILER' => ['required', Rule::in(['log', 'smtp', 'array'])],
            'MAIL_SCHEME' => ['nullable', Rule::in(['smtp', 'smtps'])],
            'MAIL_HOST' => ['nullable', 'string', 'max:160'],
            'MAIL_PORT' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'MAIL_USERNAME' => ['nullable', 'string', 'max:180'],
            'MAIL_PASSWORD' => ['nullable', 'string', 'max:1000'],
            'MAIL_FROM_ADDRESS' => ['required', 'email', 'max:180'],
            'MAIL_FROM_NAME' => ['required', 'string', 'max:120'],
        ]);
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
}
