@extends('layouts.app')

@section('title', 'Pago con Clip | '.config('app.name'))

@section('content')
<section class="container-page py-16">
    <div class="panel mx-auto max-w-2xl p-8 text-center">
        <span class="badge">Clip</span>
        <h1 class="mt-4 text-3xl font-black uppercase text-white">Pago en proceso</h1>
        <p class="mt-4 text-zinc-300">Recibimos el retorno de Clip. El webhook confirmara el estado final del pago.</p>
        @if($order)
            <a href="{{ route('checkout.received', $order) }}" class="btn-primary mt-6">Ver pedido {{ $order->folio }}</a>
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
                redirect: @json(route('checkout.received', $order)),
            }, window.location.origin);
            window.close();
        }
    </script>
@endif
@endsection
