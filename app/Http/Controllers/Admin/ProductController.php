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
        $products = Product::with('category')
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('status'), fn ($query) => $query->where('active', $request->string('status') === 'active'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
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
        $product->update(['active' => false]);

        return back()->with('status', 'Producto desactivado.');
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
