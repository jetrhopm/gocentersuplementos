<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use App\Services\MetaAdsService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cart,
        private MetaAdsService $metaAds,
    )
    {
    }

    public function index()
    {
        return view('cart.index', [
            'items' => $this->cart->items(),
            'totals' => $this->cart->totals(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::with(['category', 'variants'])->findOrFail($data['product_id']);
        $this->cart->add($product, $data['variant_id'] ?? null, (int) $data['quantity']);
        $metaEvent = $this->metaAds->browserEvent(
            'AddToCart',
            $this->metaAds->productPayload($product, (int) $data['quantity'])
        );

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'count' => $this->cart->count(),
                'meta_event' => $metaEvent,
            ]);
        }

        return back()
            ->with('status', 'Producto agregado al carrito.')
            ->with('meta_event', $metaEvent);
    }

    public function update(Request $request, string $key)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $this->cart->update($key, (int) $data['quantity']);

        if ($request->expectsJson()) {
            return $this->cartJson((int) $data['quantity'] > 0 ? 'Carrito actualizado.' : 'Producto retirado del carrito.');
        }

        return back()->with('status', 'Carrito actualizado.');
    }

    public function destroy(Request $request, string $key)
    {
        $this->cart->remove($key);

        if ($request->expectsJson()) {
            return $this->cartJson('Producto retirado del carrito.');
        }

        return back()->with('status', 'Producto retirado del carrito.');
    }

    public function clear()
    {
        $this->cart->clear();

        return redirect()->route('cart.index')->with('status', 'Carrito vaciado.');
    }

    public function applyCoupon(Request $request)
    {
        $data = $request->validate(['coupon' => ['required', 'string', 'max:40']]);
        $this->cart->applyCoupon($data['coupon']);

        if ($request->expectsJson()) {
            return $this->cartJson('Cupon aplicado.');
        }

        return back()->with('status', 'Cupon aplicado.');
    }

    public function removeCoupon(Request $request)
    {
        $this->cart->forgetCoupon();

        if ($request->expectsJson()) {
            return $this->cartJson('Cupon retirado.');
        }

        return back()->with('status', 'Cupon retirado.');
    }

    private function cartJson(string $message)
    {
        $totals = $this->cart->totals();

        return response()->json([
            'ok' => true,
            'message' => $message,
            'totals' => [
                'subtotal' => number_format($totals['subtotal'], 2),
                'shipping' => number_format($totals['shipping'], 2),
                'discount' => number_format($totals['discount'], 2),
                'total' => number_format($totals['total'], 2),
                'count' => $this->cart->count(),
            ],
            'coupon_html' => view('partials.cart-coupon', ['totals' => $totals])->render(),
        ]);
    }
}
