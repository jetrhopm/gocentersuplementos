@php($coupon = $totals['coupon'])
@if($coupon)
    <div class="coupon-box coupon-box-active">
        <div class="flex min-w-0 items-center gap-3">
            <span class="coupon-icon"><i data-lucide="ticket-check" class="h-4 w-4"></i></span>
            <div class="min-w-0">
                <div class="text-xs font-bold uppercase text-zinc-500">Cupon aplicado</div>
                <div class="truncate font-black uppercase text-white">{{ $coupon->code }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('cart.coupon.remove') }}" data-coupon-ajax>
            @csrf
            @method('DELETE')
            <button class="coupon-remove" type="submit">
                <i data-lucide="x" class="h-4 w-4"></i>
                Quitar
            </button>
        </form>
    </div>
@else
    <form method="POST" action="{{ route('cart.coupon.apply') }}" class="coupon-box" data-coupon-ajax>
        @csrf
        <label class="sr-only" for="coupon-code">Cupon</label>
        <div class="coupon-input-wrap">
            <i data-lucide="ticket-percent" class="coupon-input-icon h-4 w-4"></i>
            <input id="coupon-code" name="coupon" placeholder="Cupon" autocomplete="off">
        </div>
        <button class="btn-secondary min-h-11 px-3" type="submit">
            <i data-lucide="badge-check" class="h-4 w-4"></i>
            Aplicar
        </button>
    </form>
@endif
