<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cart)
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

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'count' => $this->cart->count()]);
        }

        return back()->with('status', 'Producto agregado al carrito.');
    }

    public function update(Request $request, string $key)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $this->cart->update($key, (int) $data['quantity']);

        return back()->with('status', 'Carrito actualizado.');
    }

    public function destroy(string $key)
    {
        $this->cart->remove($key);

        return back()->with('status', 'Producto retirado del carrito.');
    }

    public function applyCoupon(Request $request)
    {
        $data = $request->validate(['coupon' => ['required', 'string', 'max:40']]);
        $this->cart->applyCoupon($data['coupon']);

        return back()->with('status', 'Cupon aplicado.');
    }

    public function removeCoupon()
    {
        $this->cart->forgetCoupon();

        return back()->with('status', 'Cupon retirado.');
    }
}
