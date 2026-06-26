@extends('layouts.app')

@section('title', 'Consultar pedido | '.config('app.name'))

@section('content')
<section class="container-page py-12">
    <div class="grid gap-6 lg:grid-cols-[24rem_1fr]">
        <form method="POST" action="{{ route('orders.lookup.result') }}" class="panel h-fit p-6">
            @csrf
            <h1 class="text-2xl font-black uppercase text-white">Consulta tu pedido</h1>
            <div class="mt-5 grid gap-4">
                <div class="field"><label>Folio</label><input name="folio" value="{{ old('folio') }}" required></div>
                <div class="field"><label>Correo o telefono</label><input name="contact" value="{{ old('contact') }}" required></div>
                <button class="btn-primary">Buscar</button>
            </div>
        </form>
        <div>
            @isset($order)
                @if($order)
                    <div class="panel p-6">
                        <span class="badge">{{ $order->statusLabel() }}</span>
                        <h2 class="mt-4 text-2xl font-black uppercase text-white">{{ $order->folio }}</h2>
                        <div class="mt-5 grid gap-3 text-sm text-zinc-300">
                            <div>Total: <span class="price-text">${{ number_format($order->total, 2) }}</span></div>
                            <div>Guia: {{ $order->tracking_number ?: 'Pendiente' }}</div>
                        </div>
                        <a href="{{ URL::signedRoute('orders.public.show', $order) }}" class="btn-secondary mt-6">Ver detalle</a>
                    </div>
                @else
                    <div class="panel p-6 text-zinc-400">No encontramos un pedido con esos datos.</div>
                @endif
            @else
                <div class="panel p-6 text-zinc-400">Ingresa el folio y el correo o telefono usado en tu compra.</div>
            @endisset
        </div>
    </div>
</section>
@endsection
