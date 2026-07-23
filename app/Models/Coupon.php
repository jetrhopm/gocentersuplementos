<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'minimum_total',
        'max_uses',
        'uses',
        'starts_at',
        'expires_at',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'minimum_total' => 'decimal:2',
            'max_uses' => 'integer',
            'uses' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'active' => 'boolean',
        ];
    }

    public function isUsable(float $subtotal): bool
    {
        if (! $this->active || $subtotal < (float) $this->minimum_total) {
            return false;
        }

        if ($this->max_uses !== null && $this->uses >= $this->max_uses) {
            return false;
        }

        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && now()->gt($this->expires_at)) {
            return false;
        }

        return true;
    }

    public function discountFor(float $subtotal, float $shipping = 0): float
    {
        if (! $this->isUsable($subtotal)) {
            return 0;
        }

        if ($this->type === 'free_shipping') {
            return round(max(0, $shipping), 2);
        }

        if ($this->type === 'percent') {
            return round($subtotal * ((float) $this->value / 100), 2);
        }

        return min((float) $this->value, $subtotal);
    }

    public function discountLabel(): string
    {
        return match ($this->type) {
            'percent' => number_format((float) $this->value, 0).'%',
            'free_shipping' => 'Envio gratis',
            default => '$'.number_format((float) $this->value, 2),
        };
    }
}
