@extends('layouts.app')

@section('title', 'Pedido '.$order->folio.' | '.config('app.name'))

@section('content')
<section class="container-page py-10">
    <div class="grid gap-6 lg:grid-cols-[1fr_24rem]">
        <div class="grid gap-6">
            <div class="panel p-6">
                <span class="badge">{{ $order->statusLabel() }}</span>
                <h1 class="mt-4 text-3xl font-black uppercase text-white">Pedido recibido</h1>
                <p class="mt-3 text-zinc-300">Folio: <span class="accent-text font-black">{{ $order->folio }}</span></p>
            </div>

            @if($order->payment_method === 'transferencia')
                <div class="panel p-6">
                    <h2 class="text-xl font-black uppercase text-white">Instrucciones de transferencia</h2>
                    <div class="mt-5 grid gap-3 text-sm text-zinc-300">
                        <div><span class="text-zinc-500">Banco:</span> {{ $bank['bank_name'] ?: 'Configura BANK_NAME' }}</div>
                        <div><span class="text-zinc-500">Titular:</span> {{ $bank['account_holder'] ?: 'Configura BANK_ACCOUNT_HOLDER' }}</div>
                        <div><span class="text-zinc-500">Cuenta:</span> {{ $bank['account_number'] ?: 'Configura BANK_ACCOUNT_NUMBER' }}</div>
                        <div><span class="text-zinc-500">CLABE:</span> {{ $bank['clabe'] ?: 'Configura BANK_CLABE' }}</div>
                        <div><span class="text-zinc-500">Concepto:</span> {{ $order->folio }}</div>
                        <p class="leading-6 text-zinc-400">{{ $bank['instructions'] }}</p>
                    </div>
                    <form method="POST" action="{{ route('checkout.transfer.reference', $order) }}" class="mt-5 flex flex-col gap-3 sm:flex-row">
                        @csrf
                        <input name="transfer_reference" value="{{ old('transfer_reference', $order->transfer_reference) }}" placeholder="Referencia o comprobante">
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
