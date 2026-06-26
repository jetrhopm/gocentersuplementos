<article class="product-card group">
    <a href="{{ route('products.show', $product) }}" class="block">
        <div class="product-image-wrap relative aspect-[4/3] overflow-hidden">
            <img src="{{ $product->displayImage() }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
            <div class="absolute inset-x-0 top-0 flex items-start justify-between gap-3 p-3">
                <span class="rounded-full bg-black/60 px-2.5 py-1 text-xs font-bold uppercase text-zinc-100 backdrop-blur">{{ $product->category->name }}</span>
                @if($product->hasDiscount())
                    <span class="badge">-{{ $product->discountPercentage() }}%</span>
                @endif
            </div>
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/84 to-transparent"></div>
        </div>
    </a>
    <div class="grid gap-4 p-4">
        <div>
            <div class="text-xs uppercase tracking-normal text-zinc-500">{{ $product->brand ?: 'Go Center selection' }}</div>
            <a href="{{ route('products.show', $product) }}" class="product-title mt-1 block min-h-12 font-black uppercase leading-tight text-white transition">{{ $product->name }}</a>
        </div>
        <div class="flex items-end justify-between gap-3">
            <div>
                <div class="price-text text-2xl font-black">${{ number_format($product->price, 2) }}</div>
                @if($product->hasDiscount())
                    <div class="text-xs text-zinc-500 line-through">${{ number_format($product->compare_at_price, 2) }}</div>
                @endif
            </div>
            <span class="text-right text-xs text-zinc-500">{{ $product->stock > 0 ? $product->stock.' disp.' : 'Agotado' }}</span>
        </div>
        @if($product->activeVariants->isEmpty())
            <form method="POST" action="{{ route('cart.store') }}" data-cart-form>
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button class="btn-primary w-full" @disabled($product->stock < 1)>
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Agregar
                </button>
            </form>
        @else
            <a class="btn-secondary w-full" href="{{ route('products.show', $product) }}">
                <i data-lucide="sliders-horizontal" class="h-4 w-4"></i>
                Elegir variante
            </a>
        @endif
    </div>
</article>
