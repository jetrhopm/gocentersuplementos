<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('sort_order')->orderBy('name')->get();
        $sort = in_array($request->string('sort')->toString(), ['name', 'category', 'price', 'compare_at_price'], true)
            ? $request->string('sort')->toString()
            : 'created_at';
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';

        $products = Product::query()
            ->select('products.*')
            ->with(['category', 'images'])
            ->when($request->filled('q'), fn ($query) => $query->where('products.name', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('category'), fn ($query) => $query->where('products.category_id', $request->integer('category')))
            ->when($request->filled('price_min'), fn ($query) => $query->where('products.price', '>=', $request->float('price_min')))
            ->when($request->filled('price_max'), fn ($query) => $query->where('products.price', '<=', $request->float('price_max')))
            ->when($request->filled('status'), fn ($query) => $query->where('products.active', $request->string('status') === 'active'))
            ->when(
                $sort === 'category',
                fn ($query) => $query->leftJoin('categories', 'categories.id', '=', 'products.category_id')->orderBy('categories.name', $direction),
                fn ($query) => $query->orderBy("products.{$sort}", $direction)
            )
            ->orderByDesc('products.id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product(['active' => true]),
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function store(ProductRequest $request)
    {
        $product = Product::create($this->payload($request));
        $this->syncVariants($product, $request->input('variants', []));
        $this->storeImages($product, $request);

        return redirect()->route('admin.products.index')->with('status', 'Producto creado.');
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'variants']);

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $payload = $this->payload($request, $product);
        $product->fill($payload);
        $productChanged = $product->isDirty();
        $product->save();
        $this->syncVariants($product, $request->input('variants', []));
        $this->storeImages($product, $request);
        $product->refresh();
        $message = $productChanged || $request->hasFile('images') || $request->filled('variants')
            ? 'Producto actualizado. Estado actual: '.($product->active ? 'activo' : 'inactivo').'.'
            : 'No se detectaron cambios para guardar. Estado actual: '.($product->active ? 'activo' : 'inactivo').'.';

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', $message);
    }

    public function destroy(Product $product)
    {
        $product->update([
            'active' => false,
            'featured' => false,
        ]);

        return back()->with('status', 'Producto ocultado de la tienda y retirado de destacados.');
    }

    public function toggleVisibility(Product $product)
    {
        $product->update(['active' => ! $product->active]);

        $message = $product->active
            ? 'Producto visible en tienda.'
            : 'Producto oculto en tienda.';

        if ($product->active && ! $product->category?->active) {
            $message .= ' La categoria sigue inactiva, por eso aun no se mostrara al cliente.';
        }

        return back()->with('status', $message);
    }

    public function toggleFeatured(Product $product)
    {
        $product->update(['featured' => ! $product->featured]);

        return back()->with('status', $product->featured ? 'Producto agregado a destacados.' : 'Producto retirado de destacados.');
    }

    public function destroyImage(Product $product, int $image)
    {
        $productImage = $product->images()->whereKey($image)->firstOrFail();
        Storage::disk('public')->delete($productImage->path);
        $productImage->delete();

        return back()->with('status', 'Imagen eliminada.');
    }

    private function payload(ProductRequest $request, ?Product $product = null): array
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['featured'] = $request->boolean('featured');
        $data['active'] = $request->boolean('active');

        unset($data['images'], $data['variants']);

        return $data;
    }

    private function storeImages(Product $product, Request $request): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        foreach ($request->file('images') as $index => $image) {
            if (! $image) {
                continue;
            }

            $path = $image->store('products', 'public');
            $product->images()->create([
                'path' => $path,
                'alt' => $product->name,
                'sort_order' => $product->images()->count() + $index,
            ]);
        }
    }

    private function syncVariants(Product $product, array $variants): void
    {
        $seen = [];

        foreach ($variants as $variant) {
            $hasContent = collect($variant)->except(['id', 'active'])->filter(fn ($value) => $value !== null && $value !== '')->isNotEmpty();

            if (! $hasContent) {
                continue;
            }

            $payload = [
                'sku' => $variant['sku'] ?? null,
                'size' => $variant['size'] ?? null,
                'color' => $variant['color'] ?? null,
                'flavor' => $variant['flavor'] ?? null,
                'presentation' => $variant['presentation'] ?? null,
                'price_modifier' => $variant['price_modifier'] ?? 0,
                'stock' => $variant['stock'] ?? 0,
                'active' => isset($variant['active']) && (bool) $variant['active'],
            ];

            $model = ! empty($variant['id'])
                ? $product->variants()->whereKey($variant['id'])->first()
                : null;

            if ($model) {
                $model->update($payload);
            } else {
                $model = $product->variants()->create($payload);
            }

            $seen[] = $model->id;
        }

        if ($seen) {
            $product->variants()->whereNotIn('id', $seen)->update(['active' => false]);
        }
    }
}
