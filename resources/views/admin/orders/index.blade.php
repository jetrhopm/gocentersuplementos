@extends('layouts.admin')

@section('title', 'Pedidos')

@section('content')
<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div><span class="badge">Ventas</span><h1 class="mt-3 text-3xl font-black uppercase text-white">Pedidos</h1></div>
    <a href="{{ route('admin.orders.export') }}" class="btn-secondary"><i data-lucide="download" class="h-4 w-4"></i>CSV</a>
</div>
<form class="mt-6 flex flex-col gap-3 sm:flex-row">
    <input name="q" value="{{ request('q') }}" placeholder="Folio, cliente o correo">
    <select name="status">
        <option value="">Todos los estados</option>
        @foreach($statuses as $key => $label)
            <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
        @endforeach
    </select>
    <button class="btn-secondary">Filtrar</button>
</form>
<div class="panel mt-6 overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-zinc-800 text-xs uppercase text-zinc-500"><tr><th class="p-4">Folio</th><th class="p-4">Cliente</th><th class="p-4">Total</th><th class="p-4">Pago</th><th class="p-4">Estado</th><th class="p-4">Fecha</th></tr></thead>
        <tbody class="divide-y divide-zinc-800">
            @foreach($orders as $order)
                <tr>
                    <td class="p-4"><a class="accent-link font-bold text-white" href="{{ route('admin.orders.show', $order) }}">{{ $order->folio }}</a></td>
                    <td class="p-4 text-zinc-400">{{ $order->customer_name }}</td>
                    <td class="p-4 price-text">${{ number_format($order->total, 2) }}</td>
                    <td class="p-4 text-zinc-400">{{ $order->payment_method }}</td>
                    <td class="p-4"><span class="badge">{{ $order->statusLabel() }}</span></td>
                    <td class="p-4 text-zinc-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $orders->links() }}</div>
@endsection
