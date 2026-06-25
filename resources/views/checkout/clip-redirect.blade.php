@extends('layouts.app')

@section('title', 'Pago con Clip | '.config('app.name'))

@section('content')
<section class="container-page py-16">
    <div class="panel mx-auto max-w-2xl overflow-hidden p-8 text-center">
        <span class="badge">Clip</span>
        <h1 class="mt-4 text-3xl font-black uppercase text-white">Finaliza tu pago</h1>
        <p class="mt-4 text-zinc-300">Abriremos la pasarela segura de Clip para completar el pedido {{ $order->folio }}.</p>

        <div class="mt-6 rounded-lg border border-zinc-800 bg-zinc-950 p-4 text-left text-sm text-zinc-400">
            <div class="flex items-center justify-between gap-4">
                <span>Total a pagar</span>
                <strong class="price-text text-xl">${{ number_format((float) $order->total, 2) }} MXN</strong>
            </div>
        </div>

        <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
            <button type="button" class="btn-primary min-h-12" data-open-clip="{{ $clipUrl }}">
                <i data-lucide="credit-card" class="h-4 w-4"></i>
                Abrir pago seguro
            </button>
            <a href="{{ route('checkout.received', $order) }}" class="btn-secondary min-h-12">Ver pedido</a>
        </div>

        <p class="mt-5 text-xs leading-5 text-zinc-500">Si la ventana no abre, usa el boton para continuar. Tu pago se procesa directamente en Clip con sus estandares de seguridad.</p>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const button = document.querySelector('[data-open-clip]');
        const url = button?.dataset.openClip;

        if (!button || !url) {
            return;
        }

        const openClip = () => {
            const popup = window.open(url, 'clip_checkout', 'width=460,height=760,menubar=no,toolbar=no,location=yes,status=no,resizable=yes,scrollbars=yes');

            if (!popup || popup.closed || typeof popup.closed === 'undefined') {
                window.location.href = url;
            } else {
                popup.focus();
            }
        };

        button.addEventListener('click', openClip);
        window.setTimeout(openClip, 650);

        window.addEventListener('message', (event) => {
            if (event.origin !== window.location.origin || event.data?.gateway !== 'clip') {
                return;
            }

            window.location.href = event.data.redirect || @json(route('checkout.received', $order));
        });
    });
</script>
@endsection
