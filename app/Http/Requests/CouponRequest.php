<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper(trim((string) $this->input('code'))),
            'active' => $this->boolean('active'),
            'max_uses' => $this->input('max_uses') === '' ? null : $this->input('max_uses'),
            'minimum_total' => $this->input('minimum_total') === '' ? 0 : $this->input('minimum_total'),
            'value' => $this->input('type') === 'free_shipping' ? 100 : $this->input('value'),
        ]);
    }

    public function rules(): array
    {
        $coupon = $this->route('coupon');

        return [
            'code' => ['required', 'string', 'max:40', 'regex:/^[A-Z0-9_-]+$/', Rule::unique('coupons', 'code')->ignore($coupon)],
            'type' => ['required', Rule::in(['percent', 'fixed', 'free_shipping'])],
            'value' => ['required', 'numeric', 'min:0.01', 'max:999999'],
            'minimum_total' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'max_uses' => ['nullable', 'integer', 'min:1', 'max:999999'],
            'uses' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'active' => ['boolean'],
        ];
    }
}
