<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CatalogCleanupService;
use Illuminate\Http\Request;
use RuntimeException;

class CatalogCleanupController extends Controller
{
    public function index(Request $request, CatalogCleanupService $cleanup)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $categories = Category::withCount([
            'products',
            'products as product_images_count' => fn ($query) => $query->join('product_images', 'products.id', '=', 'product_images.product_id'),
            'products as product_variants_count' => fn ($query) => $query->join('product_variants', 'products.id', '=', 'product_variants.product_id'),
        ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $selectedCategory = $request->filled('category_id')
            ? Category::whereKey($request->integer('category_id'))->first()
            : null;

        return view('admin.catalog-cleanup.index', [
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'preview' => $selectedCategory ? $cleanup->preview($selectedCategory) : null,
        ]);
    }

    public function destroy(Request $request, CatalogCleanupService $cleanup)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'confirmation' => ['required', 'string'],
            'delete_files' => ['nullable', 'boolean'],
        ]);

        $category = Category::findOrFail($validated['category_id']);

        if ($validated['confirmation'] !== $category->slug) {
            return back()
                ->withInput()
                ->withErrors(['confirmation' => 'La confirmacion debe coincidir exactamente con el slug de la categoria.']);
        }

        try {
            $result = $cleanup->deleteCategoryProducts($category, $request->boolean('delete_files'));
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['cleanup' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.catalog-cleanup.index', ['category_id' => $category->id])
            ->with('status', sprintf(
                'Se borraron %d productos, %d variantes y %d registros de imagenes. Archivos fisicos borrados: %d. Compartidos conservados: %d.',
                $result['products_deleted'],
                $result['variants_deleted'],
                $result['images_deleted'],
                $result['files_deleted'],
                $result['shared_files_kept'],
            ));
    }
}
