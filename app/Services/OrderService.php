<?php

namespace App\Services;

use App\Mail\OrderReceiptMail;
use App\Models\Coupon;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private CartService $cart,
        private MetaAdsService $metaAds,
    )
    {
    }

    public function createFromCart(array $data, string $paymentMethod): Order
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Tu carrito esta vacio.']);
        }

        $order = DB::transaction(function () use ($data, $paymentMethod, $items) {
            foreach ($items as $item) {
                if ($item['quantity'] > $item['stock']) {
                    throw ValidationException::withMessages(['cart' => 'Uno o mas productos ya no tienen stock suficiente.']);
                }
            }

            $totals = $this->cart->totals();
            $status = $paymentMethod === 'clip' ? Order::STATUS_PENDING_CLIP : Order::STATUS_PENDING_TRANSFER;

            if ($totals['coupon'] instanceof Coupon) {
                $coupon = Coupon::whereKey($totals['coupon']->id)->lockForUpdate()->first();

                if (! $coupon || ! $coupon->isUsable((float) $totals['subtotal'])) {
                    throw ValidationException::withMessages(['coupon' => 'El cupon ya no esta disponible.']);
                }

                $totals['coupon'] = $coupon;
                $totals['discount'] = $coupon->discountFor((float) $totals['subtotal']);
                $totals['total'] = round(max(0, $totals['subtotal'] + $totals['shipping'] - $totals['discount']), 2);
            }

            $order = Order::create([
                'folio' => Order::makeFolio(),
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'street' => $data['street'],
                'external_number' => $data['external_number'],
                'internal_number' => $data['internal_number'] ?? null,
                'neighborhood' => $data['neighborhood'],
                'city' => $data['city'],
                'state' => $data['state'],
                'postal_code' => $data['postal_code'],
                'references' => $data['references'] ?? null,
                'subtotal' => $totals['subtotal'],
                'shipping_cost' => $totals['shipping'],
                'discount' => $totals['discount'],
                'total' => $totals['total'],
                'coupon_code' => $totals['coupon']?->code,
                'payment_method' => $paymentMethod,
                'status' => $status,
                'customer_notes' => $data['customer_notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_variant_id' => $item['variant']?->id,
                    'product_name' => $item['product']->name,
                    'variant_name' => $item['variant_label'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['total'],
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'method' => $paymentMethod,
                'provider' => $paymentMethod === 'clip' ? 'clip' : 'transferencia',
                'status' => 'pending',
                'amount' => $order->total,
                'currency' => 'MXN',
                'external_reference' => $order->folio,
            ]);

            if ($totals['coupon'] instanceof Coupon) {
                $totals['coupon']->increment('uses');
            }

            $this->cart->clear();

            return $order->load(['items', 'payment']);
        });

        try {
            Mail::to($order->customer_email)->send(new OrderReceiptMail($order));
        } catch (Throwable $exception) {
            report($exception);
        }

        return $order;
    }

    public function markAsPaid(Order $order, array $paymentData = []): Order
    {
        $sendReceipt = false;

        $order = DB::transaction(function () use ($order, $paymentData, &$sendReceipt) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $payment = $order->payment()->lockForUpdate()->first();
            $sendReceipt = $order->status !== Order::STATUS_PAID;

            if ($payment) {
                $payment->update([
                    'status' => 'paid',
                    'receipt_no' => $paymentData['receipt_no'] ?? $payment->receipt_no,
                    'transaction_id' => $paymentData['transaction_id'] ?? $payment->transaction_id,
                    'raw_response' => $paymentData['raw_response'] ?? $payment->raw_response,
                    'paid_at' => $payment->paid_at ?? now(),
                ]);
            }

            if (! $order->stock_discounted_at) {
                $this->discountStock($order);
            }

            $order->update([
                'status' => Order::STATUS_PAID,
                'stock_discounted_at' => $order->stock_discounted_at ?? now(),
            ]);

            return $order->refresh()->load(['items', 'payment']);
        });

        if ($sendReceipt) {
            try {
                Mail::to($order->customer_email)->send(new OrderReceiptMail($order));
            } catch (Throwable $exception) {
                report($exception);
            }

            $this->metaAds->sendPurchase($order);
        }

        return $order;
    }

    public function transition(Order $order, string $status, ?string $reason = null, ?string $tracking = null, ?string $notes = null): Order
    {
        if (in_array($status, [Order::STATUS_PAID, Order::STATUS_PREPARING, Order::STATUS_SHIPPED, Order::STATUS_DELIVERED], true)
            && ! $order->stock_discounted_at) {
            $order = $this->markAsPaid($order);
        }

        $payload = ['status' => $status];

        if ($reason !== null) {
            $payload['rejection_reason'] = $reason;
        }

        if ($tracking !== null) {
            $payload['tracking_number'] = $tracking;
        }

        if ($notes !== null) {
            $payload['internal_notes'] = $notes;
        }

        $order->update($payload);

        if (in_array($status, [Order::STATUS_REJECTED, Order::STATUS_CANCELLED, Order::STATUS_EXPIRED], true)) {
            $order->payment?->update(['status' => $status]);
        }

        return $order->refresh();
    }

    public function deleteWithInventoryRestore(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order = Order::with(['items', 'payment'])->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->stock_discounted_at) {
                $this->restoreStock($order);
            }

            $order->delete();
        });
    }

    private function discountStock(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            if ($item->product_variant_id) {
                $variant = ProductVariant::whereKey($item->product_variant_id)->lockForUpdate()->first();

                if ($variant) {
                    $before = $variant->stock;
                    $variant->stock = max(0, $before - $item->quantity);
                    $variant->save();

                    InventoryMovement::create([
                        'product_id' => $item->product_id,
                        'product_variant_id' => $variant->id,
                        'order_id' => $order->id,
                        'type' => 'sale',
                        'quantity' => -$item->quantity,
                        'before_stock' => $before,
                        'after_stock' => $variant->stock,
                        'notes' => 'Pedido '.$order->folio,
                    ]);
                }
            }

            $product = Product::whereKey($item->product_id)->lockForUpdate()->first();

            if ($product) {
                $before = $product->stock;
                $product->stock = max(0, $before - $item->quantity);
                $product->save();

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'product_variant_id' => $item->product_variant_id,
                    'order_id' => $order->id,
                    'type' => 'sale',
                    'quantity' => -$item->quantity,
                    'before_stock' => $before,
                    'after_stock' => $product->stock,
                    'notes' => 'Pedido '.$order->folio,
                ]);
            }
        }
    }

    private function restoreStock(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            if ($item->product_variant_id) {
                $variant = ProductVariant::whereKey($item->product_variant_id)->lockForUpdate()->first();

                if ($variant) {
                    $before = $variant->stock;
                    $variant->stock = $before + $item->quantity;
                    $variant->save();

                    InventoryMovement::create([
                        'product_id' => $item->product_id,
                        'product_variant_id' => $variant->id,
                        'order_id' => $order->id,
                        'type' => 'order_deleted_restore',
                        'quantity' => $item->quantity,
                        'before_stock' => $before,
                        'after_stock' => $variant->stock,
                        'notes' => 'Restauracion por eliminacion de pedido '.$order->folio,
                    ]);
                }
            }

            $product = Product::whereKey($item->product_id)->lockForUpdate()->first();

            if ($product) {
                $before = $product->stock;
                $product->stock = $before + $item->quantity;
                $product->save();

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'product_variant_id' => $item->product_variant_id,
                    'order_id' => $order->id,
                    'type' => 'order_deleted_restore',
                    'quantity' => $item->quantity,
                    'before_stock' => $before,
                    'after_stock' => $product->stock,
                    'notes' => 'Restauracion por eliminacion de pedido '.$order->folio,
                ]);
            }
        }
    }
}
