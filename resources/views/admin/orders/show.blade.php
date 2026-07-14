@extends('layouts.admin')

@section('title', 'Pedido '.$order->folio)

@section('content')
<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <span class="badge">{{ $order->statusLabel() }}</span>
        <h1 class="mt-3 text-3xl font-black uppercase text-white">{{ $order->folio }}</h1>
    </div>
    <div class="flex flex-wrap gap-2">
        @if($order->isPayable())
            <form method="POST" action="{{ route('admin.orders.payment-reminder', $order) }}" onsubmit="return confirm('Se enviara un correo a {{ $order->customer_email }} con el enlace de pago. ¿Continuar?');">
                @csrf
                <button type="submit" class="btn-primary"><i data-lucide="mail" class="h-4 w-4"></i>Enviar recordatorio de pago</button>
            </form>
        @endif
        <a href="{{ route('admin.orders.print', $order) }}" class="btn-secondary"><i data-lucide="printer" class="h-4 w-4"></i>Imprimir</a>
        @if(auth()->user()?->isSuperAdmin())
            <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('Se eliminara el pedido {{ $order->folio }}. Si el inventario ya fue descontado, se regresara al stock. Esta accion no se puede deshacer. Continuar?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger"><i data-lucide="trash-2" class="h-4 w-4"></i>Eliminar pedido</button>
            </form>
        @endif
        <a href="{{ route('admin.orders.index') }}" class="btn-secondary">Volver</a>
    </div>
</div>

<div class="mt-8 grid gap-6 lg:grid-cols-[1fr_24rem]">
    <div class="grid gap-6">
        <div class="panel p-5">
            <h2 class="text-xl font-black uppercase text-white">Cliente y direccion</h2>
            <div class="mt-5 grid gap-3 text-sm text-zinc-300 md:grid-cols-2">
                <div><span class="text-zinc-500">Nombre:</span> {{ $order->customer_name }}</div>
                <div><span class="text-zinc-500">Correo:</span> {{ $order->customer_email }}</div>
                <div><span class="text-zinc-500">Telefono:</span> {{ $order->customer_phone }}</div>
                <div><span class="text-zinc-500">CP:</span> {{ $order->postal_code }}</div>
                <div class="md:col-span-2"><span class="text-zinc-500">Direccion:</span> {{ $order->street }} {{ $order->external_number }} {{ $order->internal_number }}, {{ $order->neighborhood }}, {{ $order->city }}, {{ $order->state }}</div>
                <div class="md:col-span-2"><span class="text-zinc-500">Referencias:</span> {{ $order->references ?: 'Sin referencias' }}</div>
                <div class="md:col-span-2"><span class="text-zinc-500">Notas cliente:</span> {{ $order->customer_notes ?: 'Sin notas' }}</div>
            </div>
        </div>

        <div class="panel p-5">
            <h2 class="text-xl font-black uppercase text-white">Productos</h2>
            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <tbody class="divide-y divide-zinc-800">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="py-3 font-bold text-white">{{ $item->product_name }}<div class="text-xs text-zinc-500">{{ $item->variant_name }}</div></td>
                                <td class="py-3 text-zinc-400">{{ $item->quantity }}</td>
                                <td class="py-3 text-zinc-400">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-3 price-text">${{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel p-5">
            <h2 class="text-xl font-black uppercase text-white">Webhook logs</h2>
            <div class="mt-5 grid gap-2 text-sm">
                @forelse($order->webhookLogs as $log)
                    <div class="rounded-md border border-zinc-800 bg-zinc-950 p-3 text-zinc-400">
                        {{ $log->created_at->format('d/m/Y H:i') }} - {{ $log->status }} - {{ $log->payment_request_id }}
                    </div>
                @empty
                    <div class="text-zinc-500">Sin notificaciones registradas.</div>
                @endforelse
            </div>
        </div>
    </div>

    <aside class="grid h-fit gap-6">
        <div class="panel p-5">
            <h2 class="text-xl font-black uppercase text-white">Totales</h2>
            <div class="mt-5 grid gap-3 text-sm">
                <div class="flex justify-between"><span class="text-zinc-400">Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Envio</span><span>${{ number_format($order->shipping_cost, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Descuento</span><span>-${{ number_format($order->discount, 2) }}</span></div>
                <div class="flex justify-between border-t border-zinc-800 pt-3 text-lg font-black"><span>Total</span><span class="price-text">${{ number_format($order->total, 2) }}</span></div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="panel p-5">
            @csrf
            @method('PATCH')
            <h2 class="text-xl font-black uppercase text-white">Gestionar</h2>
            <div class="mt-5 grid gap-4">
                <div class="field"><label>Estado</label><select name="status">@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>@endforeach</select></div>
                <div class="field"><label>Guia</label><input name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}"></div>
                <div class="field"><label>Motivo rechazo</label><input name="rejection_reason" value="{{ old('rejection_reason', $order->rejection_reason) }}"></div>
                <div class="field"><label>Notas internas</label><textarea name="internal_notes" rows="4">{{ old('internal_notes', $order->internal_notes) }}</textarea></div>
                <button class="btn-primary"><i data-lucide="save" class="h-4 w-4"></i>Actualizar</button>
            </div>
        </form>

        <div class="panel p-5 text-sm text-zinc-300">
            <div class="font-black uppercase text-white">Pago</div>
            <div class="mt-3 grid gap-2">
                <div>Metodo: {{ $order->payment_method }}</div>
                <div>Estado pago: {{ $order->payment?->status }}</div>
                <div>Referencia: {{ $order->payment?->external_reference }}</div>
                <div>Clip ID: {{ $order->payment?->payment_request_id ?: 'N/A' }}</div>
                <div>Transferencia: {{ $order->transfer_reference ?: 'N/A' }}</div>
            </div>
        </div>
    </aside>
</div>
@endsection
