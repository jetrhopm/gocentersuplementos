@extends('layouts.app')

@section('title', 'Carrito | '.config('app.name'))

@section('content')
<section class="quiet-band">
    <div class="container-page py-10">
        <span class="badge">Compra</span>
        <h1 class="section-heading mt-3">Carrito</h1>
        <p class="mt-3 max-w-2xl text-zinc-400">Ajusta cantidades, aplica cupones y continua al pago cuando tu pedido este listo.</p>
    </div>
</section>

<section class="container-page py-10">
    <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_22rem]">
        <div class="grid gap-4">
            @forelse($items as $item)
                <div class="panel grid gap-4 p-4 sm:grid-cols-[7rem_1fr_auto] sm:items-center">
                    <img src="{{ $item['product']->displayImage() }}" alt="{{ $item['product']->name }}" class="aspect-square w-full rounded-md object-cover sm:w-28">
                    <div>
                        <a href="{{ route('products.show', $item['product']) }}" class="accent-link font-bold text-white">{{ $item['product']->name }}</a>
                        @if($item['variant_label'])
                            <div class="mt-1 text-sm text-zinc-400">{{ $item['variant_label'] }}</div>
                        @endif
                        <div class="price-text mt-2 text-sm">${{ number_format($item['unit_price'], 2) }}</div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('cart.update', $item['key']) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input class="w-20" type="number" name="quantity" value="{{ $item['quantity'] }}" min="0" max="{{ $item['stock'] }}">
                            <button class="btn-secondary px-3" aria-label="Actualizar"><i data-lucide="refresh-cw" class="h-4 w-4"></i></button>
                        </form>
                        <form method="POST" action="{{ route('cart.destroy', $item['key']) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn-danger px-3" aria-label="Quitar"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="panel p-8 text-zinc-400">Tu carrito esta vacio.</div>
            @endforelse
        </div>
        <aside class="panel h-fit overflow-hidden p-5">
            @include('partials.gocenter-brand-card', [
                'title' => 'Carrito Go Center',
                'copy' => 'Revisa tus productos antes de confirmar tu pedido.',
            ])
            <h2 class="text-xl font-black uppercase text-white">Resumen</h2>
            <div class="mt-5 grid gap-3 text-sm">
                <div class="flex justify-between"><span class="text-zinc-400">Subtotal</span><span>${{ number_format($totals['subtotal'], 2) }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Envio</span><span>${{ number_format($totals['shipping'], 2) }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Descuento</span><span>-${{ number_format($totals['discount'], 2) }}</span></div>
                <div class="border-t border-zinc-800 pt-3 text-lg font-black flex justify-between"><span>Total</span><span class="price-text">${{ number_format($totals['total'], 2) }}</span></div>
            </div>
            <div class="mt-5">
                @if($totals['coupon'])
                    <div class="notice-success mb-3 flex items-center justify-between rounded-md border px-3 py-2 text-sm">
                        {{ $totals['coupon']->code }}
                        <form method="POST" action="{{ route('cart.coupon.remove') }}">
                            @csrf
                            @method('DELETE')
                            <button class="accent-text">Quitar</button>
                        </form>
                    </div>
                @else
                    <form method="POST" action="{{ route('cart.coupon.apply') }}" class="flex gap-2">
                        @csrf
                        <input name="coupon" placeholder="Cupon" class="min-w-0 flex-1">
                        <button class="btn-secondary px-3">Aplicar</button>
                    </form>
                @endif
            </div>
            <a href="{{ route('checkout.show') }}" class="btn-primary mt-6 w-full @if($items->isEmpty()) pointer-events-none opacity-50 @endif">
                <i data-lucide="credit-card" class="h-4 w-4"></i>
                Checkout
            </a>
        </aside>
    </div>
</section>
@endsection
