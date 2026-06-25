<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $paidStatuses = [Order::STATUS_PAID, Order::STATUS_PREPARING, Order::STATUS_SHIPPED, Order::STATUS_DELIVERED];

        return view('admin.dashboard', [
            'salesToday' => Order::whereIn('status', $paidStatuses)->whereDate('created_at', today())->sum('total'),
            'salesMonth' => Order::whereIn('status', $paidStatuses)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total'),
            'pendingOrders' => Order::whereIn('status', [Order::STATUS_PENDING_CLIP, Order::STATUS_PENDING_TRANSFER])->count(),
            'lowStock' => Product::where('stock', '<=', config('services.store.low_stock_threshold'))->orderBy('stock')->take(8)->get(),
            'recentOrders' => Order::latest()->take(8)->get(),
            'bestSellers' => OrderItem::query()
                ->selectRaw('product_name, SUM(quantity) as sold')
                ->groupBy('product_name')
                ->orderByDesc('sold')
                ->take(6)
                ->get(),
        ]);
    }
}
