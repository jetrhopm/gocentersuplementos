<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Services\CartService;
use App\Services\ClipService;
use App\Services\MetaAdsService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private OrderService $orders,
        private ClipService $clip,
        private MetaAdsService $metaAds,
    ) {
    }

    public function show()
    {
        if ($this->cart->items()->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Agrega productos antes de pagar.']);
        }

        $items = $this->cart->items();
        $totals = $this->cart->totals();
        $metaInitiateCheckoutEvent = $this->metaAds->browserEvent(
            'InitiateCheckout',
            $this->metaAds->cartPayload($items, $totals)
        );

        return view('checkout.show', [
            'items' => $items,
            'totals' => $totals,
            'metaInitiateCheckoutEvent' => $metaInitiateCheckoutEvent,
        ]);
    }

    public function store(CheckoutRequest $request)
    {
        $order = $this->orders->createFromCart($request->validated(), $request->validated('payment_method'));

        if ($order->payment_method === 'clip') {
            try {
                if ($redirect = $this->startClipCheckout($order)) {
                    return $redirect;
                }
            } catch (RuntimeException $exception) {
                report($exception);

                return redirect()
                    ->route('checkout.clip.error', ['folio' => $order->folio])
                    ->withErrors(['clip' => 'No se pudo iniciar el pago con Clip. Revisa tu pedido o intenta con transferencia.']);
            }
        }

        return redirect()->to(URL::signedRoute('checkout.received', $order));
    }

    /**
     * Retoma o inicia el pago de un pedido pendiente con Clip. La ruta va
     * firmada, por lo que solo se accede desde la vista del pedido o el
     * enlace del correo (sin teclear folio ni correo).
     */
    public function pay(Request $request, Order $order)
    {
        if ($order->status === Order::STATUS_PAID) {
            return redirect()->to(URL::signedRoute('orders.public.show', $order))
                ->with('status', 'Este pedido ya esta pagado.');
        }

        if (! $order->isPayable()) {
            return redirect()->to(URL::signedRoute('orders.public.show', $order))
                ->withErrors(['pago' => 'Este pedido ya no se puede pagar en linea. Contacta a la tienda.']);
        }

        $this->ensurePayment($order);
        $order->update(['payment_method' => 'clip', 'status' => Order::STATUS_PENDING_CLIP]);
        $order->refresh()->load('payment');

        try {
            if ($redirect = $this->startClipCheckout($order)) {
                return $redirect;
            }
        } catch (RuntimeException $exception) {
            report($exception);
        }

        return redirect()->to(URL::signedRoute('orders.public.show', $order))
            ->withErrors(['pago' => 'No se pudo iniciar el pago con Clip. Intenta de nuevo en unos minutos.']);
    }

    private function startClipCheckout(Order $order)
    {
        $response = $this->clip->createCheckout($order);

        $order->payment?->update([
            'method' => 'clip',
            'provider' => 'clip',
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

        return null;
    }

    private function ensurePayment(Order $order): void
    {
        if ($order->payment) {
            return;
        }

        Payment::create([
            'order_id' => $order->id,
            'method' => 'clip',
            'provider' => 'clip',
            'status' => 'pending',
            'amount' => $order->total,
            'currency' => 'MXN',
            'external_reference' => $order->folio,
        ]);

        $order->load('payment');
    }

    public function received(Order $order)
    {
        $order->load(['items', 'payment']);

        $metaPurchaseEvent = $order->status === Order::STATUS_PAID
            ? $this->metaAds->browserEvent('Purchase', $this->metaAds->orderPayload($order), $this->metaAds->purchaseEventId($order))
            : null;

        return view('checkout.received', [
            'order' => $order,
            'bank' => config('services.bank_transfer'),
            'oxxo' => config('services.oxxo_payment'),
            'metaPurchaseEvent' => $metaPurchaseEvent,
        ]);
    }

    public function transferReference(Request $request, Order $order)
    {
        $data = $request->validate([
            'transfer_reference' => ['nullable', 'string', 'max:160'],
        ]);

        if (in_array($order->status, [Order::STATUS_PENDING_TRANSFER, Order::STATUS_PENDING_OXXO], true)) {
            $order->update(['transfer_reference' => $data['transfer_reference'] ?? null]);
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

    public function clipReturn(Order $order)
    {
        return view('checkout.clip-success', compact('order'));
    }

    public function clipCancelled(Order $order)
    {
        // No se cancela el pedido: se mantiene pendiente para que el cliente
        // pueda retomar el pago desde la vista de su pedido o el correo.
        return view('checkout.clip-error', compact('order'));
    }
}
