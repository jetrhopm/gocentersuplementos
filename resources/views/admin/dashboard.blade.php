@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <span class="badge">Dashboard</span>
        <h1 class="mt-3 text-3xl font-black uppercase text-white">Resumen</h1>
        <p class="mt-2 max-w-2xl text-sm text-zinc-400">Pulso comercial de la tienda: ventas, pedidos, stock y productos que ya estan moviendo volumen.</p>
    </div>
    <a href="{{ route('admin.orders.export') }}" class="btn-secondary">
        <i data-lucide="download" class="h-4 w-4"></i>
        Exportar CSV
    </a>
</div>

<div class="mt-8 grid gap-4 md:grid-cols-4">
    <div class="admin-stat">
        <div class="relative z-10 flex items-center justify-between gap-3">
            <div class="text-sm text-zinc-400">Ventas hoy</div>
            <i data-lucide="badge-dollar-sign" class="accent-text h-5 w-5"></i>
        </div>
        <div class="relative z-10 mt-3 text-2xl font-black price-text">${{ number_format($salesToday, 2) }}</div>
    </div>
    <div class="admin-stat">
        <div class="relative z-10 flex items-center justify-between gap-3">
            <div class="text-sm text-zinc-400">Ventas mes</div>
            <i data-lucide="trending-up" class="accent-text h-5 w-5"></i>
        </div>
        <div class="relative z-10 mt-3 text-2xl font-black price-text">${{ number_format($salesMonth, 2) }}</div>
    </div>
    <div class="admin-stat">
        <div class="relative z-10 flex items-center justify-between gap-3">
            <div class="text-sm text-zinc-400">Pendientes</div>
            <i data-lucide="clock-3" class="accent-text h-5 w-5"></i>
        </div>
        <div class="relative z-10 mt-3 text-2xl font-black text-white">{{ $pendingOrders }}</div>
    </div>
    <div class="admin-stat">
        <div class="relative z-10 flex items-center justify-between gap-3">
            <div class="text-sm text-zinc-400">Stock bajo</div>
            <i data-lucide="package-x" class="accent-text h-5 w-5"></i>
        </div>
        <div class="relative z-10 mt-3 text-2xl font-black text-white">{{ $lowStock->count() }}</div>
    </div>
</div>

<div class="mt-8 grid gap-6 lg:grid-cols-2">
    <div class="panel p-5">
        <h2 class="text-xl font-black uppercase text-white">Pedidos recientes</h2>
        <div class="mt-5 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <tbody class="divide-y divide-zinc-800">
                    @foreach($recentOrders as $order)
                        <tr>
                            <td class="py-3"><a class="accent-link font-bold text-white" href="{{ route('admin.orders.show', $order) }}">{{ $order->folio }}</a></td>
                            <td class="py-3 text-zinc-400">{{ $order->customer_name }}</td>
                            <td class="py-3 price-text">${{ number_format($order->total, 2) }}</td>
                            <td class="py-3 text-zinc-400">{{ $order->statusLabel() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel p-5">
        <h2 class="text-xl font-black uppercase text-white">Mas vendidos</h2>
        <div class="mt-5 grid gap-3">
            @forelse($bestSellers as $item)
                <div class="interactive-tile flex justify-between px-3 py-2 text-sm">
                    <span class="text-white">{{ $item->product_name }}</span>
                    <span class="accent-text">{{ $item->sold }} pzas.</span>
                </div>
            @empty
                <div class="text-sm text-zinc-500">Sin ventas aun.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="mt-8 panel p-5">
    <h2 class="text-xl font-black uppercase text-white">Productos con bajo stock</h2>
    <div class="mt-5 grid gap-3 md:grid-cols-2 lg:grid-cols-4">
        @foreach($lowStock as $product)
            <a href="{{ route('admin.products.edit', $product) }}" class="interactive-tile p-3 text-sm">
                <div class="font-bold text-white">{{ $product->name }}</div>
                <div class="mt-1 text-zinc-400">{{ $product->stock }} disponibles</div>
            </a>
        @endforeach
    </div>
</div>
@endsection
