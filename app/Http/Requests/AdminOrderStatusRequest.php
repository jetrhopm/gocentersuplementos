<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $allowed = array_keys(Order::statusesForUser($this->user(), $this->route('order')));

        return [
            'status' => ['required', Rule::in($allowed)],
            'rejection_reason' => ['nullable', 'required_if:status,'.Order::STATUS_REJECTED, 'string', 'max:255'],
            'tracking_number' => ['nullable', 'string', 'max:120'],
            'internal_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Tu usuario no tiene permiso para aplicar ese estado al pedido.',
        ];
    }
}
