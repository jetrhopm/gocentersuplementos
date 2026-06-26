@extends('layouts.app')

@section('title', 'Error de pago | '.config('app.name'))

@php
    $receivedUrl = $order ? URL::signedRoute('checkout.received', $order) : null;
@endphp

@section('content')
<section class="container-page py-16">
    <div class="panel mx-auto max-w-2xl border-red-500/40 p-8 text-center">
        <span class="badge border-red-400/40 bg-red-400/10 text-red-100">Clip</span>
        <h1 class="mt-4 text-3xl font-black uppercase text-white">No se pudo completar el pago</h1>
        <p class="mt-4 text-zinc-300">El pedido queda pendiente hasta recibir una confirmacion valida.</p>
        @if($order)
            <a href="{{ $receivedUrl }}" class="btn-secondary mt-6">Ver pedido {{ $order->folio }}</a>
        @else
            <a href="{{ route('cart.index') }}" class="btn-secondary mt-6">Volver al carrito</a>
        @endif
    </div>
</section>

@if($order)
    <script>
        if (window.opener && !window.opener.closed) {
            window.opener.postMessage({
                gateway: 'clip',
                status: 'error',
                redirect: @json($receivedUrl),
            }, window.location.origin);
            window.close();
        }
    </script>
@endif
@endsection
