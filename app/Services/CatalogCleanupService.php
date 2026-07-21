<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class CatalogCleanupService
{
    public function preview(Category $category): array
    {
        $products = $category->products()
            ->with(['images', 'variants'])
            ->orderBy('name')
            ->get();

        $paths = $products
            ->flatMap(fn (Product $product) => $product->images->pluck('path'))
            ->filter()
            ->unique()
            ->values();

        $sharedPaths = $this->sharedPaths($category, $paths);
        $imageRows = $paths->map(fn (string $path) => $this->imagePreviewRow($path, $sharedPaths))->values();

        return [
            'category' => $category,
            'products' => $products,
            'product_count' => $products->count(),
            'variant_count' => $products->sum(fn (Product $product) => $product->variants->count()),
            'image_count' => $products->sum(fn (Product $product) => $product->images->count()),
            'unique_image_count' => $paths->count(),
            'deletable_file_count' => $imageRows->where('can_delete_file', true)->count(),
            'shared_file_count' => $imageRows->where('shared', true)->count(),
            'missing_file_count' => $imageRows->where('exists', false)->where('local', true)->count(),
            'external_image_count' => $imageRows->where('local', false)->count(),
            'images' => $imageRows,
        ];
    }

    public function deleteCategoryProducts(Category $category, bool $deleteFiles = false): array
    {
        $preview = $this->preview($category);
        $productIds = $preview['products']->pluck('id')->all();

        if ($preview['product_count'] === 0) {
            throw new RuntimeException('La categoria seleccionada no tiene productos para borrar.');
        }

        $filesToDelete = $deleteFiles
            ? $preview['images']->where('can_delete_file', true)->pluck('absolute_path')->filter()->unique()->values()
            : collect();

        DB::transaction(function () use ($productIds): void {
            ProductVariant::whereIn('product_id', $productIds)->delete();
            ProductImage::whereIn('product_id', $productIds)->delete();
            Product::whereIn('id', $productIds)->delete();
        });

        $deletedFiles = 0;

        foreach ($filesToDelete as $absolutePath) {
            if (File::exists($absolutePath) && File::delete($absolutePath)) {
                $deletedFiles++;
            }
        }

        return [
            'products_deleted' => $preview['product_count'],
            'variants_deleted' => $preview['variant_count'],
            'images_deleted' => $preview['image_count'],
            'files_deleted' => $deletedFiles,
            'files_skipped' => max(0, $filesToDelete->count() - $deletedFiles),
            'shared_files_kept' => $preview['shared_file_count'],
        ];
    }

    private function sharedPaths(Category $category, Collection $paths): Collection
    {
        if ($paths->isEmpty()) {
            return collect();
        }

        return ProductImage::query()
            ->whereIn('path', $paths->all())
            ->whereHas('product', fn ($query) => $query->where('category_id', '!=', $category->id))
            ->pluck('path')
            ->unique()
            ->values();
    }

    private function imagePreviewRow(string $path, Collection $sharedPaths): array
    {
        $resolved = $this->resolveLocalPath($path);
        $isShared = $sharedPaths->contains($path);

        return [
            'path' => $path,
            'url' => $this->imageUrl($path),
            'local' => $resolved !== null,
            'absolute_path' => $resolved,
            'exists' => $resolved ? File::exists($resolved) : false,
            'shared' => $isShared,
            'can_delete_file' => $resolved !== null && File::exists($resolved) && ! $isShared,
        ];
    }

    private function resolveLocalPath(string $path): ?string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return null;
        }

        $candidate = str_starts_with($path, 'assets/')
            ? public_path($path)
            : storage_path('app/public/'.$path);

        $root = str_starts_with($path, 'assets/')
            ? realpath(public_path('assets'))
            : realpath(storage_path('app/public'));

        $directory = realpath(dirname($candidate));

        if (! $root || ! $directory || ! str_starts_with($directory, $root)) {
            return null;
        }

        return $candidate;
    }

    private function imageUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        return asset('storage/'.$path);
    }
}
