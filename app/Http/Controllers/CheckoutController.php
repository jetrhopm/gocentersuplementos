<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\ClipService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private OrderService $orders,
        private ClipService $clip,
    ) {
    }

    public function show()
    {
        if ($this->cart->items()->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Agrega productos antes de pagar.']);
        }

        return view('checkout.show', [
            'items' => $this->cart->items(),
            'totals' => $this->cart->totals(),
        ]);
    }

    public function store(CheckoutRequest $request)
    {
        $order = $this->orders->createFromCart($request->validated(), $request->validated('payment_method'));

        if ($order->payment_method === 'clip') {
            try {
                $response = $this->clip->createCheckout($order);
                $order->payment->update([
                    'status' => $response['status'] ?? 'pending',
                    'payment_request_id' => $response['payment_request_id'] ?? null,
                    'payment_request_url' => $response['payment_request_url'] ?? null,
                    'raw_response' => $response,
                ]);

                if (! empty($response['payment_request_url'])) {
                    return view('checkout.clip-redirect', [
                        'order' => $order,
                        'clipUrl' => $response['payment_request_url'],
                    ]);
                }
            } catch (RuntimeException $exception) {
                report($exception);

                return redirect()
                    ->route('checkout.clip.error', ['folio' => $order->folio])
                    ->withErrors(['clip' => $exception->getMessage()]);
            }
        }

        return redirect()->route('checkout.received', $order);
    }

    public function received(Order $order)
    {
        $order->load(['items', 'payment']);

        return view('checkout.received', [
            'order' => $order,
            'bank' => config('services.bank_transfer'),
        ]);
    }

    public function transferReference(Request $request, Order $order)
    {
        $data = $request->validate([
            'transfer_reference' => ['required', 'string', 'max:160'],
        ]);

        if ($order->status === Order::STATUS_PENDING_TRANSFER) {
            $order->update(['transfer_reference' => $data['transfer_reference']]);
        }

        return back()->with('status', 'Referencia guardada. El administrador validara tu pago.');
    }

    public function clipSuccess(Request $request)
    {
        $folio = $request->string('folio')->toString();
        $order = $folio ? Order::where('folio', $folio)->first() : null;

        return view('checkout.clip-success', compact('order'));
    }

    public function clipError(Request $request)
    {
        $folio = $request->string('folio')->toString();
        $order = $folio ? Order::where('folio', $folio)->first() : null;

        return view('checkout.clip-error', compact('order'));
    }
}
