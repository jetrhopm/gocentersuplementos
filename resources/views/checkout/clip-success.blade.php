@extends('layouts.app')

@section('title', 'Pago con tarjeta | '.config('app.name'))

@php
    $receivedUrl = $order ? URL::signedRoute('checkout.received', $order) : null;
    $isPaid = $order?->status === \App\Models\Order::STATUS_PAID;
    $isRejected = $order && in_array($order->status, [
        \App\Models\Order::STATUS_REJECTED,
        \App\Models\Order::STATUS_CANCELLED,
        \App\Models\Order::STATUS_EXPIRED,
    ], true);
@endphp

@section('content')
<section class="container-page py-16">
    <div class="panel mx-auto max-w-2xl p-8 text-center">
        <span class="badge">Tarjeta</span>
        <h1 class="mt-4 text-3xl font-black uppercase text-white">
            @if($isPaid)
                Pago aprobado
            @elseif($isRejected)
                Pago no aprobado
            @else
                Pago en confirmacion
            @endif
        </h1>
        <p class="mt-4 text-zinc-300">
            @if($isPaid)
                Gracias, tu pago fue aprobado y tu pedido ya quedo registrado como pago recibido.
            @elseif($isRejected)
                No pudimos confirmar el pago de tu pedido. Puedes revisar tu pedido e intentar nuevamente.
            @else
                Recibimos tu regreso desde Clip. Estamos confirmando el resultado de tu pago; si fue aprobado, tu pedido se actualizara en breve.
            @endif
        </p>
        @if($order)
            <a href="{{ $receivedUrl }}" class="btn-primary mt-6">Ver pedido {{ $order->folio }}</a>
        @else
            <a href="{{ route('orders.lookup') }}" class="btn-primary mt-6">Consultar pedido</a>
        @endif
    </div>
</section>

@if($order)
    <script>
        if (window.opener && !window.opener.closed) {
            window.opener.postMessage({
                gateway: 'clip',
                status: 'success',
                redirect: @json($receivedUrl),
            }, window.location.origin);
            window.close();
        }
    </script>
@endif
@endsection
