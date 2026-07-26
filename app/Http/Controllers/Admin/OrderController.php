<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminOrderStatusRequest;
use App\Mail\PaymentReminderMail;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('payment')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->string('q');
                $query->where(fn ($sub) => $sub
                    ->where('folio', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => Order::statuses(),
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['items', 'payment', 'webhookLogs']);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => Order::statusesForUser(request()->user(), $order),
        ]);
    }

    public function updateStatus(AdminOrderStatusRequest $request, Order $order, OrderService $orders)
    {
        $orders->transition(
            $order,
            $request->validated('status'),
            $request->validated('rejection_reason'),
            $request->validated('tracking_number'),
            $request->validated('internal_notes'),
        );

        return back()->with('status', 'Pedido actualizado.');
    }

    public function destroy(Request $request, Order $order, OrderService $orders)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $folio = $order->folio;
        $orders->deleteWithInventoryRestore($order);

        return redirect()
            ->route('admin.orders.index')
            ->with('status', 'Pedido '.$folio.' eliminado. Si tenia inventario descontado, fue restaurado.');
    }

    public function sendPaymentReminder(Request $request, Order $order)
    {
        if (! $order->isPayable()) {
            return back()->withErrors(['reminder' => 'Solo se puede enviar recordatorio de pedidos pendientes de pago.']);
        }

        $data = $request->validate([
            'reminder_note' => ['nullable', 'string', 'max:500'],
            'discount_type' => ['nullable', 'in:none,percent,fixed'],
            'discount_value' => ['nullable', 'numeric', 'min:0', 'max:100000'],
        ]);

        $discountApplied = $this->applyReminderDiscount(
            $order,
            $data['discount_type'] ?? 'none',
            (float) ($data['discount_value'] ?? 0)
        );

        $order->refresh()->load(['items', 'payment']);

        try {
            Mail::to($order->customer_email)->send(new PaymentReminderMail(
                $order,
                $data['reminder_note'] ?? null,
                $discountApplied
            ));
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['reminder' => 'No se pudo enviar el correo. Revisa la configuracion de correo.']);
        }

        return back()->with('status', 'Recordatorio de pago enviado a '.$order->customer_email.'.');
    }

    private function applyReminderDiscount(Order $order, ?string $type, float $value): bool
    {
        if ($value <= 0 || ! in_array($type, ['percent', 'fixed'], true)) {
            return false;
        }

        $base = round((float) $order->subtotal + (float) $order->shipping_cost, 2);
        $currentDiscount = (float) $order->discount;
        $calculatedDiscount = match ($type) {
            'percent' => round($base * min($value, 100) / 100, 2),
            default => round(min($value, $base), 2),
        };

        $newDiscount = round(max($currentDiscount, $calculatedDiscount), 2);

        if ($newDiscount <= $currentDiscount) {
            return false;
        }

        $newTotal = round(max(0, $base - $newDiscount), 2);

        $order->update([
            'discount' => $newDiscount,
            'total' => $newTotal,
        ]);

        $order->payment?->update([
            'amount' => $newTotal,
            'status' => 'pending',
        ]);

        return true;
    }

    public function print(Order $order)
    {
        $order->load(['items', 'payment']);

        return view('admin.orders.print', compact('order'));
    }

    public function export(): StreamedResponse
    {
        $filename = 'pedidos-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['folio', 'cliente', 'email', 'telefono', 'total', 'metodo', 'estado', 'fecha']);

            Order::latest()->chunk(100, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    fputcsv($handle, [
                        $order->folio,
                        $order->customer_name,
                        $order->customer_email,
                        $order->customer_phone,
                        $order->total,
                        $order->payment_method,
                        $order->status,
                        $order->created_at,
                    ]);
                }
            });

            fclose($handle);
        }, $filename);
    }
}
