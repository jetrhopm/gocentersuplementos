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

<div class="container-page pt-8">
    <a href="{{ route('products.index', ['category' => 'packs-gocenter']) }}" class="promo-banner" aria-label="Ver packs Go Center">
        <img src="{{ asset('assets/gocenter/banner.jpg') }}" alt="Go Center Suplementos" loading="lazy">
    </a>
</div>

<section class="container-page py-10">
    <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_22rem]">
        <div class="grid gap-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-black uppercase text-white">Productos en el carrito</h2>
                @if($items->isNotEmpty())
                    <form method="POST" action="{{ route('cart.clear') }}" onsubmit="return confirm('Se quitaran todos los productos del carrito. ¿Quieres continuar?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn-danger btn-clear-cart" type="submit">
                            <i data-lucide="trash" class="h-3.5 w-3.5"></i>
                            Vaciar carrito
                        </button>
                    </form>
                @endif
            </div>
            @forelse($items as $item)
                <div class="panel flex flex-wrap items-center gap-3 p-3">
                    <img src="{{ $item['product']->displayImage() }}" alt="{{ $item['product']->name }}" class="h-16 w-16 shrink-0 rounded-md object-cover">
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('products.show', $item['product']) }}" class="accent-link block truncate text-sm font-bold text-white">{{ $item['product']->name }}</a>
                        @if($item['variant_label'])
                            <div class="truncate text-xs text-zinc-400">{{ $item['variant_label'] }}</div>
                        @endif
                        <div class="price-text mt-1 text-sm">${{ number_format($item['unit_price'], 2) }}</div>
                    </div>
                    <div class="flex w-full shrink-0 items-center justify-end gap-1.5 sm:w-auto">
                        <form method="POST" action="{{ route('cart.update', $item['key']) }}" class="flex items-center gap-1.5">
                            @csrf
                            @method('PATCH')
                            <input class="w-14 text-center" type="number" name="quantity" value="{{ $item['quantity'] }}" min="0" max="{{ $item['stock'] }}" inputmode="numeric" aria-label="Cantidad de {{ $item['product']->name }}">
                            <button class="btn-secondary px-2.5" aria-label="Actualizar"><i data-lucide="refresh-cw" class="h-4 w-4"></i></button>
                        </form>
                        <form method="POST" action="{{ route('cart.destroy', $item['key']) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn-danger px-2.5" aria-label="Quitar"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
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
                <div class="flex justify-between"><span class="text-zinc-400">Subtotal</span><span data-cart-subtotal>${{ number_format($totals['subtotal'], 2) }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Envio</span><span data-cart-shipping>${{ number_format($totals['shipping'], 2) }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Descuento</span><span data-cart-discount>-${{ number_format($totals['discount'], 2) }}</span></div>
                <div class="border-t border-zinc-800 pt-3 text-lg font-black flex justify-between"><span>Total</span><span class="price-text" data-cart-total>${{ number_format($totals['total'], 2) }}</span></div>
            </div>
            <div class="mt-5" data-coupon-area>
                @include('partials.cart-coupon')
            </div>
            <a
                href="{{ route('checkout.show') }}"
                class="btn-primary mt-6 w-full @if($items->isEmpty()) pointer-events-none opacity-50 @endif"
                @if($items->isEmpty()) aria-disabled="true" tabindex="-1" @endif
            >
                <i data-lucide="credit-card" class="h-4 w-4"></i>
                Ir a pagar
            </a>
        </aside>
    </div>
</section>
@endsection
