@extends('layouts.app')

@section('title', 'Pedido '.$order->folio.' | '.config('app.name'))

@if(! empty($metaPurchaseEvent))
    @push('scripts')
        <script>
            window.goMetaTrack?.(
                @json($metaPurchaseEvent['name']),
                @json($metaPurchaseEvent['custom_data']),
                @json($metaPurchaseEvent['event_id'])
            );
        </script>
    @endpush
@endif

@section('content')
<section class="container-page py-10" data-checkout-complete>
    <div class="grid gap-6 lg:grid-cols-[1fr_24rem]">
        <div class="grid gap-6">
            <div class="panel p-6">
                <span class="badge">{{ $order->statusLabel() }}</span>
                <h1 class="mt-4 text-3xl font-black uppercase text-white">Pedido recibido</h1>
                <p class="mt-3 text-zinc-300">Folio: <span class="accent-text font-black">{{ $order->folio }}</span></p>
            </div>

            <div class="panel p-6">
                <div class="flex items-center gap-3">
                    <span class="checkout-heading-icon"><i data-lucide="user-round" class="h-5 w-5"></i></span>
                    <div>
                        <h2 class="text-xl font-black uppercase text-white">Datos del comprador</h2>
                        <p class="mt-1 text-sm text-zinc-500">Informacion registrada para contacto y entrega.</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 text-sm text-zinc-300 md:grid-cols-2">
                    <div class="rounded-lg border border-zinc-800 bg-zinc-950/70 p-4">
                        <div class="text-xs uppercase text-zinc-500">Nombre completo</div>
                        <div class="mt-1 font-bold text-white">{{ $order->customer_name }}</div>
                    </div>
                    <div class="rounded-lg border border-zinc-800 bg-zinc-950/70 p-4">
                        <div class="text-xs uppercase text-zinc-500">Correo</div>
                        <div class="mt-1 break-words font-bold text-white">{{ $order->customer_email }}</div>
                    </div>
                    <div class="rounded-lg border border-zinc-800 bg-zinc-950/70 p-4">
                        <div class="text-xs uppercase text-zinc-500">Telefono</div>
                        <div class="mt-1 font-bold text-white">{{ $order->customer_phone }}</div>
                    </div>
                    <div class="rounded-lg border border-zinc-800 bg-zinc-950/70 p-4">
                        <div class="text-xs uppercase text-zinc-500">Codigo postal</div>
                        <div class="mt-1 font-bold text-white">{{ $order->postal_code }}</div>
                    </div>
                    <div class="rounded-lg border border-zinc-800 bg-zinc-950/70 p-4 md:col-span-2">
                        <div class="text-xs uppercase text-zinc-500">Direccion</div>
                        <div class="mt-1 leading-6 text-white">
                            {{ $order->street }} {{ $order->external_number }}
                            @if($order->internal_number)
                                Int. {{ $order->internal_number }}
                            @endif
                            , {{ $order->neighborhood }}, {{ $order->city }}, {{ $order->state }}
                        </div>
                    </div>
                    @if($order->references)
                        <div class="rounded-lg border border-zinc-800 bg-zinc-950/70 p-4 md:col-span-2">
                            <div class="text-xs uppercase text-zinc-500">Referencias</div>
                            <div class="mt-1 leading-6 text-white">{{ $order->references }}</div>
                        </div>
                    @endif
                </div>
            </div>

            @if($order->isPayable())
                <div class="panel p-6">
                    <div class="flex items-center gap-3">
                        <span class="checkout-heading-icon"><i data-lucide="credit-card" class="h-5 w-5"></i></span>
                        <div>
                            <h2 class="text-xl font-black uppercase text-white">Termina tu pago</h2>
                            <p class="mt-1 text-sm text-zinc-400">Tu pedido esta reservado y pendiente de pago. Puedes pagar en linea de forma segura.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ URL::signedRoute('checkout.pay', $order) }}" class="mt-5">
                        @csrf
                        <button class="btn-primary min-h-12 w-full sm:w-auto" type="submit">
                            <i data-lucide="credit-card" class="h-4 w-4"></i>
                            Pagar con Clip
                        </button>
                    </form>
                </div>
            @endif

            @if($order->payment_method === 'transferencia')
                <div class="panel p-6">
                    <h2 class="text-xl font-black uppercase text-white">Instrucciones de transferencia</h2>
                    <div class="mt-5 grid gap-3 text-sm text-zinc-300">
                        <div><span class="text-zinc-500">Banco:</span> {{ $bank['bank_name'] ?: 'Configura BANK_NAME' }}</div>
                        <div><span class="text-zinc-500">Titular:</span> {{ $bank['account_holder'] ?: 'Configura BANK_ACCOUNT_HOLDER' }}</div>
                        <div><span class="text-zinc-500">Cuenta:</span> {{ $bank['account_number'] ?: 'Configura BANK_ACCOUNT_NUMBER' }}</div>
                        <div><span class="text-zinc-500">CLABE:</span> {{ $bank['clabe'] ?: 'Configura BANK_CLABE' }}</div>
                        <div class="rounded-lg border border-zinc-800 bg-zinc-950 p-4">
                            <div class="text-zinc-500">Concepto sugerido</div>
                            <div class="mt-1 text-lg font-black text-white">{{ $order->folio }}</div>
                            <p class="mt-2 leading-6 text-zinc-400">Si tu banco no permite letras o guiones en el concepto, usa esta referencia numerica:</p>
                            <div class="mt-2 inline-flex rounded-md border border-red-500/40 bg-red-500/10 px-3 py-2 text-xl font-black text-red-100">
                                {{ $order->transferNumericReference() }}
                            </div>
                        </div>
                        <p class="leading-6 text-zinc-400">{{ $bank['instructions'] }}</p>
                    </div>
                    <div class="mt-5 rounded-lg border border-amber-400/30 bg-amber-400/10 p-4 text-sm leading-6 text-amber-100">
                        Registrar la referencia es opcional, pero nos ayuda a identificar tu pago mas rapido. Si tu banco genero una referencia propia o no te dejo poner concepto, puedes escribir la referencia que aparece en tu comprobante.
                    </div>
                    <form method="POST" action="{{ URL::signedRoute('checkout.transfer.reference', $order) }}" class="mt-5 flex flex-col gap-3 sm:flex-row">
                        @csrf
                        <input
                            name="transfer_reference"
                            value="{{ old('transfer_reference', $order->transfer_reference) }}"
                            placeholder="Referencia usada o generada por tu banco (opcional)"
                            maxlength="160"
                        >
                        <button class="btn-secondary">Guardar referencia</button>
                    </form>
                </div>
            @endif

            <div class="panel p-6">
                <h2 class="text-xl font-black uppercase text-white">Productos</h2>
                <div class="mt-5 grid gap-3">
                    @foreach($order->items as $item)
                        <div class="flex justify-between gap-4 border-b border-zinc-800 pb-3 text-sm last:border-0">
                            <div>
                                <div class="font-bold text-white">{{ $item->product_name }}</div>
                                <div class="text-zinc-500">{{ $item->variant_name }} - {{ $item->quantity }} pza.</div>
                            </div>
                            <div class="price-text">${{ number_format($item->total, 2) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <aside class="panel h-fit overflow-hidden p-6">
            @include('partials.gocenter-brand-card', [
                'title' => 'Pedido confirmado',
                'copy' => 'Guarda tu folio para consultar el estado de tu compra.',
            ])
            <h2 class="text-xl font-black uppercase text-white">Total</h2>
            <div class="mt-5 grid gap-3 text-sm">
                <div class="flex justify-between"><span class="text-zinc-400">Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Envio</span><span>${{ number_format($order->shipping_cost, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Descuento</span><span>-${{ number_format($order->discount, 2) }}</span></div>
                <div class="flex justify-between border-t border-zinc-800 pt-3 text-lg font-black"><span>Total</span><span class="price-text">${{ number_format($order->total, 2) }}</span></div>
            </div>
            @if(config('services.store.whatsapp'))
                <a class="btn-primary mt-6 w-full" href="https://wa.me/{{ preg_replace('/\D+/', '', config('services.store.whatsapp')) }}?text={{ urlencode('Hola, quiero preguntar por mi pedido '.$order->folio) }}">
                    <i data-lucide="message-circle" class="h-4 w-4"></i>
                    WhatsApp
                </a>
            @endif
        </aside>
    </div>
</section>
@endsection
