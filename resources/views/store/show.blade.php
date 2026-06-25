@extends('layouts.app')

@section('title', ($product->meta_title ?: $product->name).' | '.config('app.name'))
@section('meta_description', $product->meta_description ?: \Illuminate\Support\Str::limit($product->description, 150))

@section('content')
<section class="container-page py-10">
    <div class="grid gap-8 lg:grid-cols-[1.08fr_.92fr]">
        <div class="grid gap-4">
            <div class="hero-panel overflow-hidden">
                <img src="{{ $product->displayImage() }}" alt="{{ $product->name }}" class="aspect-[4/3] w-full object-cover">
            </div>
            @if($product->images->count() > 1)
                <div class="grid grid-cols-4 gap-3">
                    @foreach($product->images as $image)
                        <img src="{{ $image->url() }}" alt="{{ $image->alt ?: $product->name }}" class="aspect-square rounded-md border border-zinc-800 object-cover">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="panel h-fit p-6 lg:p-8" x-data="{ variant: '', qty: 1 }">
            <a href="{{ route('categories.show', $product->category) }}" class="badge">{{ $product->category->name }}</a>
            <h1 class="mt-4 text-4xl font-black uppercase leading-none text-white sm:text-5xl">{{ $product->name }}</h1>
            <div class="mt-2 text-sm uppercase tracking-normal text-zinc-500">{{ $product->brand }}</div>
            <div class="mt-5 flex items-end gap-3">
                <div class="price-text text-4xl font-black">${{ number_format($product->price, 2) }}</div>
                @if($product->hasDiscount())
                    <div class="pb-1 text-zinc-500 line-through">${{ number_format($product->compare_at_price, 2) }}</div>
                    <span class="badge">-{{ $product->discountPercentage() }}%</span>
                @endif
            </div>
            <p class="mt-5 leading-7 text-zinc-300">{{ $product->description }}</p>

            <form method="POST" action="{{ route('cart.store') }}" class="mt-6 grid gap-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                @if($product->activeVariants->isNotEmpty())
                    <div class="field">
                        <label>Variante</label>
                        <select name="variant_id" x-model="variant" required>
                            <option value="">Selecciona talla, sabor o presentacion</option>
                            @foreach($product->activeVariants as $variant)
                                <option value="{{ $variant->id }}">{{ $variant->label() ?: 'Variante' }} - {{ $variant->stock }} disp. @if($variant->price_modifier != 0) - {{ $variant->price_modifier > 0 ? '+' : '' }}${{ number_format($variant->price_modifier, 2) }} @endif</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="field max-w-32">
                    <label>Cantidad</label>
                    <input type="number" name="quantity" min="1" max="99" x-model="qty" value="1">
                </div>
                <button class="btn-primary min-h-12" @disabled($product->stock < 1)>
                    <i data-lucide="shopping-cart" class="h-4 w-4"></i>
                    Agregar al carrito
                </button>
            </form>
            <div class="mt-6 grid gap-3 border-t divider-line pt-5 text-sm text-zinc-400">
                <div class="flex items-center gap-2"><i data-lucide="boxes" class="accent-text h-4 w-4"></i> Stock base: {{ $product->stock }}</div>
                <div class="flex items-center gap-2"><i data-lucide="shield-check" class="accent-text h-4 w-4"></i> Pago con transferencia o Clip, procesado con estandares de seguridad.</div>
            </div>
        </div>
    </div>

    @if($related->isNotEmpty())
        <div class="mt-14">
            <span class="badge">Tambien puede servirte</span>
            <h2 class="section-heading mt-3">Relacionados</h2>
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($related as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    @endif
</section>
@endsection
