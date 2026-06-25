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
        ]);
    }

    public function rules(): array
    {
        return [
            'APP_NAME' => ['required', 'string', 'max:80'],
            'APP_URL' => ['required', 'url', 'max:180'],
            'STORE_META_DESCRIPTION' => ['nullable', 'string', 'max:220'],
            'STORE_WHATSAPP' => ['nullable', 'string', 'max:30'],
            'STORE_THEME' => ['required', Rule::in(['volt', 'ember', 'glacier', 'gocenter'])],
            'STORE_SHIPPING_COST' => ['required', 'numeric', 'min:0', 'max:99999'],
            'STORE_FREE_SHIPPING_FROM' => ['required', 'numeric', 'min:0', 'max:999999'],
            'STORE_LOW_STOCK_THRESHOLD' => ['required', 'integer', 'min:0', 'max:9999'],
            'STORE_MAX_UPLOAD_KB' => ['required', 'integer', 'min:512', 'max:10240'],
            'STORE_MAINTENANCE_MODE' => ['boolean'],
            'STORE_HERO_CAROUSEL_SLUGS' => ['nullable', 'string', 'max:1000'],
            'STORE_PRODUCT_CAROUSEL_SLUGS' => ['nullable', 'string', 'max:1400'],

            'BANK_NAME' => ['nullable', 'string', 'max:120'],
            'BANK_ACCOUNT_HOLDER' => ['nullable', 'string', 'max:160'],
            'BANK_ACCOUNT_NUMBER' => ['nullable', 'string', 'max:80'],
            'BANK_CLABE' => ['nullable', 'string', 'max:30'],
            'BANK_TRANSFER_INSTRUCTIONS' => ['nullable', 'string', 'max:500'],

            'CLIP_BASE_URL' => ['required', 'url', 'max:180'],
            'CLIP_AUTH_SCHEME' => ['required', Rule::in(['Bearer', 'Basic'])],
            'CLIP_PUBLIC_KEY' => ['nullable', 'string', 'max:1000'],
            'CLIP_SECRET_KEY' => ['nullable', 'string', 'max:1000'],
            'CLIP_API_KEY' => ['nullable', 'string', 'max:1000'],
            'CLIP_WEBHOOK_SECRET' => ['nullable', 'string', 'max:1000'],
            'CLIP_WEBHOOK_URL' => ['nullable', 'url', 'max:220'],
            'CLIP_SUCCESS_URL' => ['nullable', 'url', 'max:220'],
            'CLIP_ERROR_URL' => ['nullable', 'url', 'max:220'],

            'MAIL_MAILER' => ['required', Rule::in(['log', 'smtp', 'array'])],
            'MAIL_SCHEME' => ['nullable', 'string', 'max:20'],
            'MAIL_HOST' => ['nullable', 'string', 'max:160'],
            'MAIL_PORT' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'MAIL_USERNAME' => ['nullable', 'string', 'max:180'],
            'MAIL_PASSWORD' => ['nullable', 'string', 'max:1000'],
            'MAIL_FROM_ADDRESS' => ['required', 'email', 'max:180'],
            'MAIL_FROM_NAME' => ['required', 'string', 'max:120'],
        ];
    }
}
