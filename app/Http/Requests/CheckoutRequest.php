<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'customer_name' => trim((string) $this->customer_name),
            'customer_email' => strtolower(trim((string) $this->customer_email)),
            'customer_phone' => preg_replace('/\D+/', '', (string) $this->customer_phone),
            'postal_code' => preg_replace('/\D+/', '', (string) $this->postal_code),
            'payment_method' => trim((string) $this->payment_method),
        ]);
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'min:5', 'max:140', 'regex:/^\S+\s+\S+/u'],
            'customer_email' => ['required', 'email:rfc', 'max:160'],
            'customer_phone' => ['required', 'digits:10'],
            'street' => ['required', 'string', 'max:160'],
            'external_number' => ['required', 'string', 'max:30'],
            'internal_number' => ['nullable', 'string', 'max:30'],
            'neighborhood' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
            'state' => ['required', 'string', 'max:120'],
            'postal_code' => ['required', 'digits:5'],
            'references' => ['nullable', 'string', 'max:500'],
            'customer_notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:transferencia,clip'],
            'website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.regex' => 'Escribe nombre y apellido.',
            'customer_phone.digits' => 'El telefono debe tener exactamente 10 digitos.',
            'postal_code.digits' => 'El codigo postal debe tener exactamente 5 digitos.',
        ];
    }
}
