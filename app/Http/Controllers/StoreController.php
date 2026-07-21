<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Services\MetaAdsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function __construct(private MetaAdsService $metaAds)
    {
    }

    public function home()
    {
        $featured = Product::active()
            ->featured()
            ->with(['category', 'images', 'activeVariants'])
            ->latest()
            ->take(8)
            ->get();

        $offers = Product::active()
            ->whereNotNull('compare_at_price')
            ->with(['category', 'images'])
            ->take(4)
            ->get();

        $heroProducts = $this->productsBySlugs(config('services.store.hero_carousel_slugs', []), 6);
        $carouselProducts = $this->productsBySlugs(config('services.store.product_carousel_slugs', []), 12);
        $categories = Category::active()->orderBy('sort_order')->get();

        return view('store.home', compact('featured', 'offers', 'categories', 'heroProducts', 'carouselProducts'));
    }

    public function products(Request $request)
    {
        return $this->catalog($request);
    }

    public function category(Request $request, Category $category)
    {
        $request->merge(['category' => $category->slug]);

        return $this->catalog($request, $category);
    }

    public function offers(Request $request)
    {
        $category = Category::where('slug', 'ofertas')->firstOrFail();
        $request->merge(['category' => $category->slug]);

        return $this->catalog($request, $category);
    }

    private function catalog(Request $request, ?Category $currentCategory = null)
    {
        $query = Product::active()->with(['category', 'images', 'activeVariants']);

        if ($request->filled('q')) {
            $search = trim($request->string('q'));
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn (Builder $category) => $category->where('slug', $request->string('category')));
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->string('brand'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        if ($request->boolean('available')) {
            $query->where('stock', '>', 0);
        }

        if ($request->filled('size')) {
            $query->whereHas('activeVariants', fn (Builder $variant) => $variant->where('size', $request->string('size')));
        }

        if ($request->filled('sort')) {
            match ($request->string('sort')->toString()) {
                'price_asc' => $query->orderBy('price'),
                'price_desc' => $query->orderByDesc('price'),
                'newest' => $query->latest(),
                default => $query->orderByDesc('featured')->latest(),
            };
        } else {
            $query->orderByDesc('featured')->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->orderBy('sort_order')->get();
        $brands = Product::active()->whereNotNull('brand')->distinct()->orderBy('brand')->pluck('brand');
        $sizes = \App\Models\ProductVariant::whereNotNull('size')->distinct()->orderBy('size')->pluck('size');

        $metaSearchEvent = null;

        if ($request->filled('q')) {
            $metaSearchEvent = $this->metaAds->browserEvent('Search', [
                'search_string' => trim($request->string('q')),
                'content_ids' => $products->getCollection()->map(fn (Product $product) => (string) ($product->sku ?: $product->slug ?: $product->id))->values()->all(),
                'content_type' => 'product',
            ]);
        }

        return view('store.products', compact('products', 'categories', 'brands', 'sizes', 'currentCategory', 'metaSearchEvent'));
    }

    private function productsBySlugs(array $slugs, int $take)
    {
        $slugs = collect($slugs)
            ->map(fn ($slug) => trim((string) $slug))
            ->filter()
            ->unique()
            ->values();

        if ($slugs->isEmpty()) {
            return collect();
        }

        return Product::active()
            ->with(['category', 'images', 'activeVariants'])
            ->whereIn('slug', $slugs)
            ->get()
            ->sortBy(fn (Product $product) => $slugs->search($product->slug))
            ->take($take)
            ->values();
    }

    public function show(Product $product)
    {
        abort_unless($product->active && $product->category?->active, 404);

        $product->load(['category', 'images', 'activeVariants']);
        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->with('images')
            ->take(4)
            ->get();

        $metaViewContentEvent = $this->metaAds->browserEvent(
            'ViewContent',
            $this->metaAds->productPayload($product)
        );

        return view('store.show', compact('product', 'related', 'metaViewContentEvent'));
    }

    public function lookup()
    {
        return view('orders.lookup');
    }

    public function lookupResult(Request $request)
    {
        $data = $request->validate([
            'folio' => ['required', 'string', 'max:40'],
            'contact' => ['required', 'string', 'max:160'],
        ]);

        $contact = strtolower(trim($data['contact']));
        $digits = preg_replace('/\D+/', '', $contact);

        $order = Order::with(['items', 'payment'])
            ->where('folio', strtoupper(trim($data['folio'])))
            ->where(function (Builder $query) use ($contact, $digits) {
                $query->where('customer_email', $contact);

                if ($digits) {
                    $query->orWhere('customer_phone', $digits);
                }
            })
            ->first();

        return view('orders.lookup', compact('order'));
    }

    public function publicOrder(Order $order)
    {
        $order->load(['items', 'payment']);

        $metaPurchaseEvent = $order->status === Order::STATUS_PAID
            ? $this->metaAds->browserEvent('Purchase', $this->metaAds->orderPayload($order), $this->metaAds->purchaseEventId($order))
            : null;

        return view('checkout.received', [
            'order' => $order,
            'bank' => config('services.bank_transfer'),
            'metaPurchaseEvent' => $metaPurchaseEvent,
        ]);
    }

    public function sitemap()
    {
        $products = Product::active()->latest()->get();
        $categories = Category::active()->orderBy('sort_order')->get();

        return response()
            ->view('store.sitemap', compact('products', 'categories'))
            ->header('Content-Type', 'application/xml');
    }
}
