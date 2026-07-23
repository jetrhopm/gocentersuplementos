<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CartService
{
    private string $sessionKey = 'cart.items';

    public function raw(): array
    {
        return session($this->sessionKey, []);
    }

    public function add(Product $product, ?int $variantId, int $quantity): void
    {
        $quantity = max(1, min(99, $quantity));
        $variant = $this->resolveVariant($product, $variantId);
        $stock = $product->availableStock($variant);
        $key = $this->key($product->id, $variant?->id);
        $cart = $this->raw();
        $current = (int) ($cart[$key]['quantity'] ?? 0);

        if (! $product->active || ! $product->category?->active) {
            throw ValidationException::withMessages(['product_id' => 'El producto no esta disponible.']);
        }

        if ($current + $quantity > $stock) {
            throw ValidationException::withMessages(['quantity' => 'No hay stock suficiente para agregar esa cantidad.']);
        }

        $cart[$key] = [
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
            'quantity' => $current + $quantity,
        ];

        session([$this->sessionKey => $cart]);
    }

    public function update(string $key, int $quantity): void
    {
        $cart = $this->raw();

        if (! isset($cart[$key])) {
            return;
        }

        if ($quantity <= 0) {
            unset($cart[$key]);
            session([$this->sessionKey => $cart]);

            return;
        }

        $quantity = min(99, $quantity);
        $product = Product::with(['category', 'variants'])->findOrFail($cart[$key]['product_id']);
        $variant = $this->resolveVariant($product, $cart[$key]['variant_id'] ?? null);

        if ($quantity > $product->availableStock($variant)) {
            throw ValidationException::withMessages(['quantity' => 'La cantidad supera el stock disponible.']);
        }

        $cart[$key]['quantity'] = $quantity;
        session([$this->sessionKey => $cart]);
    }

    public function remove(string $key): void
    {
        $cart = $this->raw();
        unset($cart[$key]);
        session([$this->sessionKey => $cart]);
    }

    public function clear(): void
    {
        session()->forget([$this->sessionKey, 'cart.coupon']);
    }

    public function count(): int
    {
        return $this->items()->sum('quantity');
    }

    public function items(): Collection
    {
        $valid = [];

        $items = collect($this->raw())->map(function (array $line, string $key) use (&$valid) {
            $product = Product::with(['category', 'images', 'variants'])->find($line['product_id'] ?? null);

            if (! $product || ! $product->active || ! $product->category?->active) {
                return null;
            }

            $variant = null;
            if (! empty($line['variant_id'])) {
                $variant = $product->variants->firstWhere('id', $line['variant_id']);
                if (! $variant || ! $variant->active) {
                    return null;
                }
            }

            $stock = $product->availableStock($variant);

            if ($stock < 1) {
                return null;
            }

            $quantity = max(1, min((int) ($line['quantity'] ?? 1), $stock));

            if ($quantity < 1) {
                return null;
            }

            $valid[$key] = [
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'quantity' => $quantity,
            ];

            $unitPrice = (float) $product->price + (float) ($variant?->price_modifier ?? 0);

            return [
                'key' => $key,
                'product' => $product,
                'variant' => $variant,
                'variant_label' => $variant?->label(),
                'quantity' => $quantity,
                'unit_price' => round($unitPrice, 2),
                'total' => round($unitPrice * $quantity, 2),
                'stock' => $stock,
            ];
        })->filter()->values();

        if ($valid !== $this->raw()) {
            session([$this->sessionKey => $valid]);
        }

        return $items;
    }

    public function subtotal(): float
    {
        return round($this->items()->sum('total'), 2);
    }

    public function coupon(): ?Coupon
    {
        $code = session('cart.coupon');

        if (! $code) {
            return null;
        }

        return Coupon::where('code', $code)->first();
    }

    public function applyCoupon(string $code): void
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        if (! $coupon || ! $coupon->isUsable($this->subtotal())) {
            throw ValidationException::withMessages(['coupon' => 'El cupon no es valido para este pedido.']);
        }

        session(['cart.coupon' => $coupon->code]);
    }

    public function forgetCoupon(): void
    {
        session()->forget('cart.coupon');
    }

    public function totals(): array
    {
        $subtotal = $this->subtotal();
        $coupon = $this->coupon();
        $shippingCost = (float) config('services.store.shipping_cost');
        $freeShippingFrom = (float) config('services.store.free_shipping_from');
        $shipping = $subtotal > 0 ? $shippingCost : 0;
        $hasFreeShipping = $subtotal > 0 && $subtotal >= $freeShippingFrom;

        if ($hasFreeShipping) {
            $shipping = 0;
        }

        $discount = $coupon?->discountFor($subtotal, $shipping) ?? 0;

        return [
            'subtotal' => round($subtotal, 2),
            'shipping' => round($shipping, 2),
            'shipping_cost' => round($shippingCost, 2),
            'free_shipping_from' => round($freeShippingFrom, 2),
            'free_shipping_remaining' => round(max(0, $freeShippingFrom - $subtotal), 2),
            'has_free_shipping' => $hasFreeShipping,
            'discount' => round($discount, 2),
            'total' => round(max(0, $subtotal + $shipping - $discount), 2),
            'coupon' => $coupon,
        ];
    }

    private function key(int $productId, ?int $variantId): string
    {
        return $productId.':'.($variantId ?: 'base');
    }

    private function resolveVariant(Product $product, ?int $variantId): ?ProductVariant
    {
        if (! $variantId) {
            return null;
        }

        $variant = $product->variants->firstWhere('id', $variantId)
            ?: ProductVariant::where('product_id', $product->id)->whereKey($variantId)->first();

        if (! $variant || ! $variant->active) {
            throw ValidationException::withMessages(['variant_id' => 'La variante seleccionada no existe.']);
        }

        return $variant;
    }
}
