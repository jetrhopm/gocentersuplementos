<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'size',
        'color',
        'flavor',
        'presentation',
        'price_modifier',
        'stock',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'price_modifier' => 'decimal:2',
            'stock' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function label(): string
    {
        return collect([$this->size, $this->color, $this->flavor, $this->presentation])
            ->filter()
            ->implode(' / ');
    }

    public function finalPrice(): float
    {
        return (float) $this->product->price + (float) $this->price_modifier;
    }
}
