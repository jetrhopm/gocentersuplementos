@extends('layouts.app')

@section('title', 'Finalizar pedido | '.config('app.name'))

@php
    $shippingCost = $totals['shipping_cost'] ?? (float) config('services.store.shipping_cost');
    $freeShippingFrom = $totals['free_shipping_from'] ?? (float) config('services.store.free_shipping_from');
    $freeShippingRemaining = $totals['free_shipping_remaining'] ?? max(0, $freeShippingFrom - $totals['subtotal']);
    $hasFreeShipping = $totals['has_free_shipping'] ?? false;
@endphp

@section('content')
<section class="quiet-band">
    <div class="container-page py-10">
        <span class="badge">Pago seguro</span>
        <h1 class="section-heading mt-3">Finalizar pedido</h1>
        <p class="mt-3 max-w-2xl text-zinc-400">Captura tus datos, elige el metodo de pago y recibe tu folio al terminar.</p>
    </div>
</section>

<section class="container-page py-10">
    <form method="POST" action="{{ route('checkout.store') }}" class="grid gap-6 lg:grid-cols-[1fr_24rem]">
        @csrf
        <div class="grid gap-6">
            <div class="panel checkout-panel p-5">
                <div class="checkout-heading">
                    <span class="checkout-heading-icon"><i data-lucide="contact-round" class="h-5 w-5"></i></span>
                    <div>
                        <h2 class="text-xl font-black uppercase text-white">Datos de contacto</h2>
                        <p class="mt-1 text-sm text-zinc-500">Usaremos estos datos para confirmar el pedido.</p>
                    </div>
                </div>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div class="field md:col-span-2">
                        <label><i data-lucide="user-round" class="h-4 w-4"></i>Nombre completo</label>
                        <div class="input-shell">
                            <i data-lucide="user-round" class="input-icon"></i>
                            <input name="customer_name" value="{{ old('customer_name') }}" placeholder="Nombre y apellido" required>
                        </div>
                    </div>
                    <div class="field">
                        <label><i data-lucide="mail" class="h-4 w-4"></i>Correo electronico</label>
                        <div class="input-shell">
                            <i data-lucide="mail" class="input-icon"></i>
                            <input type="email" name="customer_email" value="{{ old('customer_email') }}" placeholder="correo@ejemplo.com" required>
                        </div>
                    </div>
                    <div class="field">
                        <label><i data-lucide="phone" class="h-4 w-4"></i>Telefono</label>
                        <div class="input-shell">
                            <i data-lucide="phone" class="input-icon"></i>
                            <input name="customer_phone" value="{{ old('customer_phone') }}" inputmode="numeric" maxlength="10" placeholder="10 digitos" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel checkout-panel p-5">
                <div class="checkout-heading">
                    <span class="checkout-heading-icon"><i data-lucide="map-pinned" class="h-5 w-5"></i></span>
                    <div>
                        <h2 class="text-xl font-black uppercase text-white">Direccion</h2>
                        <p class="mt-1 text-sm text-zinc-500">Captura una direccion completa para evitar retrasos.</p>
                    </div>
                </div>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div class="field md:col-span-2">
                        <label><i data-lucide="route" class="h-4 w-4"></i>Calle</label>
                        <div class="input-shell">
                            <i data-lucide="route" class="input-icon"></i>
                            <input name="street" value="{{ old('street') }}" placeholder="Nombre de la calle" required>
                        </div>
                    </div>
                    <div class="address-pair md:contents">
                        <div class="field compact-field">
                            <label><i data-lucide="hash" class="h-4 w-4"></i>Numero exterior</label>
                            <div class="input-shell">
                                <i data-lucide="hash" class="input-icon"></i>
                                <input name="external_number" value="{{ old('external_number') }}" required>
                            </div>
                        </div>
                        <div class="field compact-field">
                            <label><i data-lucide="door-open" class="h-4 w-4"></i>Numero interior</label>
                            <div class="input-shell">
                                <i data-lucide="door-open" class="input-icon"></i>
                                <input name="internal_number" value="{{ old('internal_number') }}" placeholder="Opcional">
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label><i data-lucide="map" class="h-4 w-4"></i>Colonia</label>
                        <div class="input-shell">
                            <i data-lucide="map" class="input-icon"></i>
                            <input name="neighborhood" value="{{ old('neighborhood') }}" required>
                        </div>
                    </div>
                    <div class="field">
                        <label><i data-lucide="building-2" class="h-4 w-4"></i>Ciudad o municipio</label>
                        <div class="input-shell">
                            <i data-lucide="building-2" class="input-icon"></i>
                            <input name="city" value="{{ old('city') }}" required>
                        </div>
                    </div>
                    <div class="field">
                        <label><i data-lucide="map-pin" class="h-4 w-4"></i>Estado</label>
                        <div class="input-shell">
                            <i data-lucide="map-pin" class="input-icon"></i>
                            <input name="state" value="{{ old('state') }}" required>
                        </div>
                    </div>
                    <div class="field">
                        <label><i data-lucide="mailbox" class="h-4 w-4"></i>Codigo postal</label>
                        <div class="input-shell">
                            <i data-lucide="mailbox" class="input-icon"></i>
                            <input name="postal_code" value="{{ old('postal_code') }}" inputmode="numeric" maxlength="5" placeholder="5 digitos" required>
                        </div>
                    </div>
                    <div class="field md:col-span-2">
                        <label><i data-lucide="navigation" class="h-4 w-4"></i>Referencias</label>
                        <div class="input-shell textarea">
                            <i data-lucide="navigation" class="input-icon"></i>
                            <textarea name="references" rows="3" placeholder="Entre calles, color de fachada, punto de entrega">{{ old('references') }}</textarea>
                        </div>
                    </div>
                    <div class="field md:col-span-2">
                        <label><i data-lucide="notebook-pen" class="h-4 w-4"></i>Notas del pedido</label>
                        <div class="input-shell textarea">
                            <i data-lucide="notebook-pen" class="input-icon"></i>
                            <textarea name="customer_notes" rows="3" placeholder="Indicaciones especiales opcionales">{{ old('customer_notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel checkout-panel p-5">
                <div class="checkout-heading">
                    <span class="checkout-heading-icon"><i data-lucide="wallet-cards" class="h-5 w-5"></i></span>
                    <div>
                        <h2 class="text-xl font-black uppercase text-white">Metodo de pago</h2>
                        <p class="mt-1 text-sm text-zinc-500">Elige como quieres confirmar tu pedido.</p>
                    </div>
                </div>
                <div class="mt-5 grid gap-3">
                    <label class="payment-card bank">
                        <input type="radio" name="payment_method" value="transferencia" @checked(old('payment_method', 'transferencia') === 'transferencia') class="sr-only">
                        <span class="payment-icon"><i data-lucide="landmark" class="h-6 w-6"></i></span>
                        <span class="min-w-0">
                            <span class="flex flex-wrap items-center gap-2">
                                <span class="font-black uppercase text-white">Transferencia bancaria</span>
                                <span class="badge">Manual</span>
                            </span>
                            <span class="mt-2 block text-sm leading-6 text-zinc-400">Genera el folio y te muestra los datos bancarios para pagar. El admin valida el comprobante.</span>
                            <span class="mt-3 inline-flex items-center gap-2 text-xs font-bold uppercase accent-text">
                                <i data-lucide="receipt-text" class="h-3.5 w-3.5"></i>
                                Pedido pendiente de aprobacion
                            </span>
                        </span>
                        <span class="payment-check"><i data-lucide="check" class="h-4 w-4"></i></span>
                    </label>
                    <label class="payment-card clip">
                        <input type="radio" name="payment_method" value="clip" @checked(old('payment_method') === 'clip') class="sr-only">
                        <span class="payment-icon"><i data-lucide="credit-card" class="h-6 w-6"></i></span>
                        <span class="min-w-0">
                            <span class="flex flex-wrap items-center gap-2">
                                <span class="font-black uppercase text-white">Clip Checkout</span>
                                <span class="badge">Tarjeta externa</span>
                            </span>
                            <span class="mt-2 block text-sm leading-6 text-zinc-400">Tu pago sera procesado por Clip con estandares de seguridad para pagos en linea.</span>
                            <span class="mt-3 inline-flex items-center gap-2 text-xs font-bold uppercase accent-text">
                                <i data-lucide="shield-check" class="h-3.5 w-3.5"></i>
                                Redireccion segura
                            </span>
                        </span>
                        <span class="payment-check"><i data-lucide="check" class="h-4 w-4"></i></span>
                    </label>
                </div>
            </div>
        </div>

        <aside class="panel checkout-panel h-fit overflow-hidden p-5">
            @include('partials.gocenter-brand-card', [
                'title' => 'Pago Go Center',
                'copy' => 'Confirma tus datos y elige el metodo de pago seguro.',
            ])
            <div class="checkout-heading">
                <span class="checkout-heading-icon"><i data-lucide="shopping-bag" class="h-5 w-5"></i></span>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Tu pedido</h2>
                    <p class="mt-1 text-sm text-zinc-500">Resumen antes de confirmar.</p>
                </div>
            </div>
            <div class="mt-5 grid gap-4">
                @foreach($items as $item)
                    <div class="interactive-tile flex gap-3 p-3">
                        <img src="{{ $item['product']->displayImage() }}" alt="{{ $item['product']->name }}" class="h-16 w-16 rounded-md object-cover">
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-bold text-white">{{ $item['product']->name }}</div>
                            <div class="text-xs text-zinc-500">{{ $item['quantity'] }} x ${{ number_format($item['unit_price'], 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="shipping-callout mt-5">
                <div class="flex items-start gap-3">
                    <span class="checkout-heading-icon h-9 w-9 rounded-md"><i data-lucide="{{ $hasFreeShipping ? 'badge-check' : 'truck' }}" class="h-4 w-4"></i></span>
                    <div class="min-w-0">
                        @if($hasFreeShipping)
                            <div class="font-black uppercase text-white">Envio gratis aplicado</div>
                            <p class="mt-1 text-sm leading-6 text-zinc-400">Tu compra supera los ${{ number_format($freeShippingFrom, 0) }} MXN, por eso el costo de envio se descuenta del resumen.</p>
                        @else
                            <div class="font-black uppercase text-white">Envio ${{ number_format($shippingCost, 0) }} MXN</div>
                            <p class="mt-1 text-sm leading-6 text-zinc-400">Te faltan <span class="accent-text font-black">${{ number_format($freeShippingRemaining, 2) }}</span> para obtener envio gratis desde ${{ number_format($freeShippingFrom, 0) }} MXN.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-5 grid gap-3 border-t border-zinc-800 pt-5 text-sm">
                <div class="checkout-summary-row"><span><i data-lucide="receipt" class="h-4 w-4"></i>Subtotal</span><span>${{ number_format($totals['subtotal'], 2) }}</span></div>
                <div class="checkout-summary-row"><span><i data-lucide="truck" class="h-4 w-4"></i>Envio</span><span class="{{ $hasFreeShipping ? 'accent-text font-black' : '' }}">{{ $hasFreeShipping ? 'Gratis' : '$'.number_format($totals['shipping'], 2) }}</span></div>
                <div class="checkout-summary-row"><span><i data-lucide="badge-percent" class="h-4 w-4"></i>Descuento</span><span>-${{ number_format($totals['discount'], 2) }}</span></div>
                <div class="checkout-summary-row border-t border-zinc-800 pt-3 text-lg font-black"><span class="text-white"><i data-lucide="badge-dollar-sign" class="h-5 w-5 accent-text"></i>Total</span><span class="price-text">${{ number_format($totals['total'], 2) }}</span></div>
            </div>
            <button class="btn-primary mt-6 w-full min-h-12">
                <i data-lucide="shield-check" class="h-4 w-4"></i>
                Confirmar pedido
            </button>
        </aside>
    </form>
</section>
@endsection
