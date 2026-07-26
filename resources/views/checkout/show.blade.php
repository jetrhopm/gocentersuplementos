@extends('layouts.app')

@section('title', 'Pagar pedido | '.config('app.name'))

@if(! empty($metaInitiateCheckoutEvent))
    @push('scripts')
        <script>
            window.goMetaTrack?.(
                @json($metaInitiateCheckoutEvent['name']),
                @json($metaInitiateCheckoutEvent['custom_data']),
                @json($metaInitiateCheckoutEvent['event_id'])
            );
        </script>
    @endpush
@endif

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
        <h1 class="section-heading mt-3">Pagar pedido</h1>
        <p class="mt-3 max-w-2xl text-zinc-400">Captura tus datos, elige el metodo de pago y recibe tu folio al terminar.</p>
    </div>
</section>

<div class="container-page pt-8">
    <div class="promo-banner">
        <img src="{{ asset('assets/brand/banner.jpg') }}" alt="Go Center Suplementos" width="1280" height="720" loading="lazy" decoding="async">
    </div>
</div>

<section class="container-page py-10">
    <div class="grid gap-6 lg:grid-cols-[1fr_24rem]">
        <form
            method="POST"
            action="{{ route('checkout.store') }}"
            class="grid gap-6"
            data-checkout-wizard
            data-postal-lookup-url="{{ url('/api/codigos-postales/__CP__') }}"
        >
            @csrf
            <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
        <div class="grid gap-6">
            <div class="checkout-wizard-header panel p-4">
                <div class="checkout-stepper" aria-label="Progreso del pedido">
                    <button type="button" class="checkout-step-pill active" data-checkout-goto="0">
                        <span>1</span>
                        Contacto
                    </button>
                    <button type="button" class="checkout-step-pill" data-checkout-goto="1">
                        <span>2</span>
                        Envio
                    </button>
                    <button type="button" class="checkout-step-pill" data-checkout-goto="2">
                        <span>3</span>
                        Pago
                    </button>
                </div>
                <div class="mt-4 flex items-center justify-between gap-3 rounded-lg border border-zinc-800 bg-zinc-950/70 px-3 py-2 text-xs text-zinc-500">
                    <span data-checkout-draft-status>Guardamos tus datos de envio en este dispositivo.</span>
                    <button type="button" class="font-black uppercase tracking-wide text-zinc-300 transition hover:text-white" data-checkout-draft-clear>
                        Limpiar datos
                    </button>
                </div>
            </div>

            <div class="checkout-wizard-stage">
            <div class="panel checkout-panel checkout-step active p-5" data-checkout-step="0">
                <div class="checkout-heading">
                    <span class="checkout-heading-icon"><i data-lucide="contact-round" class="h-5 w-5"></i></span>
                    <div>
                        <h2 class="text-xl font-black uppercase text-white">Datos de contacto</h2>
                        <p class="mt-1 text-sm text-zinc-500">Usaremos estos datos para confirmar el pedido.</p>
                    </div>
                </div>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div class="field md:col-span-2">
                        <label for="customer_name"><i data-lucide="user-round" class="h-4 w-4"></i>Nombre completo</label>
                        <div class="input-shell">
                            <i data-lucide="user-round" class="input-icon"></i>
                            <input
                                id="customer_name"
                                name="customer_name"
                                value="{{ old('customer_name') }}"
                                placeholder="Nombre y apellido"
                                autocomplete="name"
                                minlength="5"
                                pattern="\S+\s+\S+.*"
                                title="Escribe nombre y apellido."
                                data-error-message="Escribe nombre y apellido."
                                required
                            >
                        </div>
                    </div>
                    <div class="field">
                        <label for="customer_email"><i data-lucide="mail" class="h-4 w-4"></i>Correo electronico</label>
                        <div class="input-shell">
                            <i data-lucide="mail" class="input-icon"></i>
                            <input
                                type="email"
                                id="customer_email"
                                name="customer_email"
                                value="{{ old('customer_email') }}"
                                placeholder="correo@ejemplo.com"
                                autocomplete="email"
                                spellcheck="false"
                                autocapitalize="off"
                                data-error-message="Escribe un correo valido."
                                required
                            >
                        </div>
                    </div>
                    <div class="field">
                        <label for="customer_phone"><i data-lucide="phone" class="h-4 w-4"></i>Telefono</label>
                        <div class="input-shell">
                            <i data-lucide="phone" class="input-icon"></i>
                            <input
                                type="tel"
                                id="customer_phone"
                                name="customer_phone"
                                value="{{ old('customer_phone') }}"
                                inputmode="numeric"
                                autocomplete="tel-national"
                                minlength="10"
                                maxlength="10"
                                pattern="\d{10}"
                                placeholder="10 digitos"
                                title="El telefono debe tener exactamente 10 digitos."
                                data-error-message="El telefono debe tener exactamente 10 digitos."
                                required
                            >
                        </div>
                    </div>
                </div>
                <div class="checkout-step-actions">
                    <button type="button" class="btn-primary min-h-12" data-checkout-next>
                        Continuar
                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                    </button>
                </div>
            </div>

            <div class="panel checkout-panel checkout-step p-5" data-checkout-step="1" hidden>
                <div class="checkout-heading">
                    <span class="checkout-heading-icon"><i data-lucide="map-pinned" class="h-5 w-5"></i></span>
                    <div>
                        <h2 class="text-xl font-black uppercase text-white">Direccion de envio</h2>
                        <p class="mt-1 text-sm text-zinc-500">Enviaremos tu pedido por paqueteria a la direccion que nos indiques.</p>
                    </div>
                </div>
                <div class="mt-5 rounded-lg border border-red-500/25 bg-red-500/10 p-4 text-sm leading-6 text-red-50">
                    Enviaremos tu pedido por paqueteria a la direccion que nos indiques. Por ahora, las compras realizadas en la pagina se entregan unicamente a domicilio.
                </div>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div class="field md:col-span-2">
                        <label for="street"><i data-lucide="route" class="h-4 w-4"></i>Calle</label>
                        <div class="input-shell">
                            <i data-lucide="route" class="input-icon"></i>
                            <input id="street" name="street" value="{{ old('street') }}" placeholder="Nombre de la calle" autocomplete="address-line1" required>
                        </div>
                    </div>
                    <div class="address-pair md:contents">
                        <div class="field compact-field">
                            <label for="external_number"><i data-lucide="hash" class="h-4 w-4"></i>Numero exterior</label>
                            <div class="input-shell">
                                <i data-lucide="hash" class="input-icon"></i>
                                <input id="external_number" name="external_number" value="{{ old('external_number') }}" required>
                            </div>
                        </div>
                        <div class="field compact-field">
                            <label for="internal_number"><i data-lucide="door-open" class="h-4 w-4"></i>Numero interior</label>
                            <div class="input-shell">
                                <i data-lucide="door-open" class="input-icon"></i>
                                <input id="internal_number" name="internal_number" value="{{ old('internal_number') }}" placeholder="Opcional">
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label for="postal_code"><i data-lucide="mailbox" class="h-4 w-4"></i>Codigo postal</label>
                        <div class="input-shell">
                            <i data-lucide="mailbox" class="input-icon"></i>
                            <input
                                id="postal_code"
                                name="postal_code"
                                value="{{ old('postal_code') }}"
                                inputmode="numeric"
                                autocomplete="postal-code"
                                minlength="5"
                                maxlength="5"
                                pattern="\d{5}"
                                placeholder="5 digitos"
                                title="El codigo postal debe tener exactamente 5 digitos."
                                data-error-message="El codigo postal debe tener exactamente 5 digitos."
                                data-postal-code-field
                                required
                            >
                        </div>
                        <p class="postal-lookup-message" data-postal-message aria-live="polite"></p>
                    </div>
                    <div class="field">
                        <label for="neighborhood"><i data-lucide="map" class="h-4 w-4"></i>Colonia</label>
                        <div class="input-shell neighborhood-combobox" data-neighborhood-combobox>
                            <i data-lucide="map" class="input-icon"></i>
                            <input
                                id="neighborhood"
                                name="neighborhood"
                                value="{{ old('neighborhood') }}"
                                placeholder="Escribe o elige tu colonia"
                                autocomplete="address-level3"
                                role="combobox"
                                aria-expanded="false"
                                aria-controls="neighborhood-options"
                                aria-autocomplete="list"
                                data-neighborhood-field
                                required
                            >
                            <button type="button" class="neighborhood-toggle" data-neighborhood-toggle aria-label="Mostrar colonias">
                                <i data-lucide="chevron-down" class="h-4 w-4"></i>
                            </button>
                            <div class="neighborhood-options" id="neighborhood-options" role="listbox" aria-label="Colonias sugeridas" data-neighborhood-options hidden></div>
                        </div>
                    </div>
                    <div class="field">
                        <label for="city"><i data-lucide="building-2" class="h-4 w-4"></i>Ciudad o municipio</label>
                        <div class="input-shell">
                            <i data-lucide="building-2" class="input-icon"></i>
                            <input id="city" name="city" value="{{ old('city') }}" autocomplete="address-level2" data-city-field required>
                        </div>
                    </div>
                    <div class="field">
                        <label for="state"><i data-lucide="map-pin" class="h-4 w-4"></i>Estado</label>
                        <div class="input-shell">
                            <i data-lucide="map-pin" class="input-icon"></i>
                            <input id="state" name="state" value="{{ old('state') }}" autocomplete="address-level1" data-state-field required>
                        </div>
                    </div>
                    <div class="field md:col-span-2">
                        <label for="references"><i data-lucide="navigation" class="h-4 w-4"></i>Referencias</label>
                        <div class="input-shell textarea">
                            <i data-lucide="navigation" class="input-icon"></i>
                            <textarea id="references" name="references" rows="3" placeholder="Entre calles, color de fachada, punto de entrega">{{ old('references') }}</textarea>
                        </div>
                    </div>
                    <div class="field md:col-span-2">
                        <label for="customer_notes"><i data-lucide="notebook-pen" class="h-4 w-4"></i>Notas del pedido</label>
                        <div class="input-shell textarea">
                            <i data-lucide="notebook-pen" class="input-icon"></i>
                            <textarea id="customer_notes" name="customer_notes" rows="3" placeholder="Indicaciones especiales opcionales">{{ old('customer_notes') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="checkout-step-actions">
                    <button type="button" class="btn-secondary min-h-12" data-checkout-prev>
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                        Regresar
                    </button>
                    <button type="button" class="btn-primary min-h-12" data-checkout-next>
                        Continuar
                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                    </button>
                </div>
            </div>

            <div class="panel checkout-panel checkout-step p-5" data-checkout-step="2" hidden>
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
                                <span class="badge">Validacion bancaria</span>
                            </span>
                            <span class="mt-2 block text-sm leading-6 text-zinc-400">Tu pedido quedara reservado mientras realizas la transferencia. Una vez confirmado el pago, comenzaremos a prepararlo. La validacion puede tomar de 0 a 48 horas habiles.</span>
                            <span class="mt-3 inline-flex items-center gap-2 text-xs font-bold uppercase accent-text">
                                <i data-lucide="receipt-text" class="h-3.5 w-3.5"></i>
                                Se prepara al confirmar el pago
                            </span>
                        </span>
                        <span class="payment-check"><i data-lucide="check" class="h-4 w-4"></i></span>
                    </label>
                    <label class="payment-card clip">
                        <input type="radio" name="payment_method" value="clip" @checked(old('payment_method') === 'clip') class="sr-only">
                        <span class="payment-icon"><i data-lucide="credit-card" class="h-6 w-6"></i></span>
                        <span class="min-w-0">
                            <span class="flex flex-wrap items-center gap-2">
                                <span class="font-black uppercase text-white">Tarjeta de debito o credito</span>
                                <span class="badge">Pago seguro</span>
                            </span>
                            <span class="mt-2 block text-sm leading-6 text-zinc-400">Paga de forma segura con tarjeta. El cobro se procesa mediante una pasarela protegida.</span>
                            <span class="mt-3 inline-flex items-center gap-2 text-xs font-bold uppercase accent-text">
                                <i data-lucide="shield-check" class="h-3.5 w-3.5"></i>
                                Redireccion segura
                            </span>
                        </span>
                        <span class="payment-check"><i data-lucide="check" class="h-4 w-4"></i></span>
                    </label>
                    <label class="payment-card oxxo">
                        <input type="radio" name="payment_method" value="oxxo" @checked(old('payment_method') === 'oxxo') class="sr-only">
                        <span class="payment-icon"><i data-lucide="qr-code" class="h-6 w-6"></i></span>
                        <span class="min-w-0">
                            <span class="flex flex-wrap items-center gap-2">
                                <span class="font-black uppercase text-white">Pago en OXXO</span>
                                <span class="badge">QR</span>
                            </span>
                            <span class="mt-2 block text-sm leading-6 text-zinc-400">Genera tu pedido y te mostramos un codigo QR para pagar en tienda OXXO. Tambien se envia a tu correo.</span>
                            <span class="mt-3 inline-flex items-center gap-2 text-xs font-bold uppercase accent-text">
                                <i data-lucide="store" class="h-3.5 w-3.5"></i>
                                Validacion manual del pago
                            </span>
                        </span>
                        <span class="payment-check"><i data-lucide="check" class="h-4 w-4"></i></span>
                    </label>
                </div>
                <div class="checkout-step-actions">
                    <button type="button" class="btn-secondary min-h-12" data-checkout-prev>
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                        Regresar
                    </button>
                    <button class="btn-primary min-h-12">
                        <i data-lucide="shield-check" class="h-4 w-4"></i>
                        Confirmar pedido
                    </button>
                </div>
            </div>
            </div>
        </div>
        </form>

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
                        <img src="{{ $item['product']->displayImage() }}" alt="{{ $item['product']->name }}" width="64" height="64" loading="lazy" decoding="async" class="h-16 w-16 rounded-md object-cover">
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-bold text-white">{{ $item['product']->name }}</div>
                            <div class="text-xs text-zinc-500">{{ $item['quantity'] }} x ${{ number_format($item['unit_price'], 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-5" data-coupon-area>
                @include('partials.cart-coupon')
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
                <div class="checkout-summary-row"><span><i data-lucide="receipt" class="h-4 w-4"></i>Subtotal</span><span data-cart-subtotal>${{ number_format($totals['subtotal'], 2) }}</span></div>
                <div class="checkout-summary-row"><span><i data-lucide="truck" class="h-4 w-4"></i>Envio</span><span class="{{ $hasFreeShipping ? 'accent-text font-black' : '' }}" data-cart-shipping>{{ $hasFreeShipping ? 'Gratis' : '$'.number_format($totals['shipping'], 2) }}</span></div>
                <div class="checkout-summary-row"><span><i data-lucide="badge-percent" class="h-4 w-4"></i>Descuento</span><span data-cart-discount>-${{ number_format($totals['discount'], 2) }}</span></div>
                <div class="checkout-summary-row border-t border-zinc-800 pt-3 text-lg font-black"><span class="text-white"><i data-lucide="badge-dollar-sign" class="h-5 w-5 accent-text"></i>Total</span><span class="price-text" data-cart-total>${{ number_format($totals['total'], 2) }}</span></div>
            </div>
            <button type="button" class="btn-primary mt-6 w-full min-h-12 lg:hidden" data-checkout-outside-next>
                <i data-lucide="arrow-right" class="h-4 w-4"></i>
                Continuar
            </button>
        </aside>
    </div>
</section>
@endsection
