<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            'statuses' => Order::statuses(),
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
