<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Phar;
use PharData;
use RuntimeException;

class CatalogBackupService
{
    public function summary(): array
    {
        $categories = $this->orderedCategories()
            ->withCount(['products', 'products as product_images_count' => function ($query) {
                $query->join('product_images', 'product_images.product_id', '=', 'products.id');
            }])
            ->get();

        return [
            'categories' => $categories->count(),
            'category_banners' => $categories->filter(fn (Category $category) => $this->categoryBanner($category) !== null)->count(),
            'products' => Product::count(),
            'product_images' => ProductImage::count(),
            'product_variants' => ProductVariant::count(),
            'category_rows' => $categories
                ->map(fn (Category $category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'products_count' => (int) $category->products_count,
                    'product_images_count' => (int) $category->product_images_count,
                    'has_banner' => $this->categoryBanner($category) !== null,
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array<int, int|string>  $categoryIds
     */
    public function create(array $categoryIds): array
    {
        if (! class_exists(PharData::class)) {
            throw new RuntimeException('El servidor no tiene disponible PharData para generar el backup.');
        }

        $categoryIds = collect($categoryIds)
            ->map(fn (int|string $id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($categoryIds->isEmpty()) {
            throw new RuntimeException('Selecciona al menos una categoria para respaldar.');
        }

        $directory = storage_path('app/catalog-backups');
        File::ensureDirectoryExists($directory);

        $baseName = 'backup-catalogo-productos-'.now()->format('Ymd-His');
        $tarPath = $directory.DIRECTORY_SEPARATOR.$baseName.'.tar';
        $gzipPath = $tarPath.'.gz';

        File::delete([$tarPath, $gzipPath]);

        $categories = $this->orderedCategories()
            ->whereIn('id', $categoryIds)
            ->with(['products' => fn (HasMany $query) => $query->with(['category', 'images', 'variants'])->orderBy('id')])
            ->get();

        if ($categories->isEmpty()) {
            throw new RuntimeException('No se encontraron categorias validas para respaldar.');
        }

        $globalMedia = [];
        $addedFiles = [];

        $archive = new PharData($tarPath);

        foreach ($categories as $category) {
            $categoryMedia = [];
            $folder = $this->categoryFolder($category);
            $banner = $this->categoryBanner($category);

            if ($banner) {
                $bannerArchivePath = $folder.'/'.$banner['archive_path'];
                $this->addLocalFile($archive, $banner['absolute_path'], $bannerArchivePath, $addedFiles);
                $categoryMedia[] = $banner + [
                    'type' => 'category_banner',
                    'category_slug' => $category->slug,
                    'archive_path' => $bannerArchivePath,
                ];
            }

            foreach ($category->products as $product) {
                foreach ($product->images as $image) {
                    $resolved = $this->resolveProductImage($image);

                    if (($resolved['included'] ?? false) && isset($resolved['absolute_path'], $resolved['archive_path'])) {
                        $resolved['archive_path'] = $folder.'/'.$resolved['archive_path'];
                        $this->addLocalFile($archive, $resolved['absolute_path'], $resolved['archive_path'], $addedFiles);
                    }

                    $categoryMedia[] = array_merge($resolved, [
                        'type' => 'product_image',
                        'product_slug' => $product->slug,
                        'product_name' => $product->name,
                        'image_id' => $image->id,
                    ]);
                }
            }

            $categoryPayload = $this->categoryPayload($category, $categoryMedia);
            $archive->addFromString($folder.'/categoria.json', json_encode($categoryPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $archive->addFromString($folder.'/categoria.sql', $this->categorySqlDump($category));
            $archive->addFromString($folder.'/media-manifest.json', json_encode($categoryMedia, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            $globalMedia = array_merge($globalMedia, $categoryMedia);
        }

        $products = $categories->flatMap(fn (Category $category) => $category->products);
        $payload = [
            'generated_at' => now()->toIso8601String(),
            'app_url' => config('app.url'),
            'contents' => [
                'categories' => 'Cada categoria seleccionada tiene su propia carpeta.',
                'sql' => 'Cada carpeta contiene categoria.sql separado por categoria.',
                'json' => 'Cada carpeta contiene categoria.json separado por categoria.',
                'files' => 'Solo archivos locales encontrados por categoria. Imagenes externas quedan como URL.',
            ],
            'summary' => [
                'categories' => $categories->count(),
                'products' => $products->count(),
                'product_images' => $products->sum(fn (Product $product) => $product->images->count()),
                'product_variants' => $products->sum(fn (Product $product) => $product->variants->count()),
                'included_files' => count($addedFiles),
            ],
            'categories' => $this->categoriesPayload($categories),
            'media' => $globalMedia,
        ];

        $archive->addFromString('manifest.json', json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $archive->addFromString('media-manifest.json', json_encode($globalMedia, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $archive->addFromString('README.txt', $this->readme());

        $archive->compress(Phar::GZ);
        unset($archive);

        File::delete($tarPath);

        return [
            'path' => $gzipPath,
            'filename' => $baseName.'.tar.gz',
        ];
    }

    private function orderedCategories()
    {
        return Category::orderBy('sort_order')->orderBy('id');
    }

    /**
     * @param  Collection<int, Category>  $categories
     */
    private function categoriesPayload(Collection $categories): array
    {
        return $categories
            ->map(function (Category $category) {
                $banner = $this->categoryBanner($category);

                return [
                    'data' => $category->getAttributes(),
                    'banner' => $banner ? [
                        'path' => $banner['path'],
                        'archive_path' => $banner['archive_path'],
                    ] : null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, Product>  $products
     */
    private function categoryPayload(Category $category, array $media): array
    {
        return [
            'generated_at' => now()->toIso8601String(),
            'category' => [
                'data' => $category->getAttributes(),
                'banner' => collect($media)->firstWhere('type', 'category_banner'),
            ],
            'summary' => [
                'products' => $category->products->count(),
                'product_images' => $category->products->sum(fn (Product $product) => $product->images->count()),
                'product_variants' => $category->products->sum(fn (Product $product) => $product->variants->count()),
            ],
            'products' => $this->productsPayload($category->products, $this->categoryFolder($category)),
            'media' => $media,
        ];
    }

    /**
     * @param  Collection<int, Product>  $products
     */
    private function productsPayload(Collection $products, ?string $folder = null): array
    {
        return $products
            ->map(fn (Product $product) => [
                'data' => $product->getAttributes(),
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'slug' => $product->category->slug,
                    'name' => $product->category->name,
                ] : null,
                'images' => $product->images->map(function (ProductImage $image) use ($folder) {
                    $resolved = $this->resolveProductImage($image);
                    $archivePath = $resolved['archive_path'] ?? null;

                    if ($archivePath && $folder) {
                        $archivePath = $folder.'/'.$archivePath;
                    }

                    return [
                        'data' => $image->getAttributes(),
                        'file' => [
                            'included' => $resolved['included'],
                            'source_type' => $resolved['source_type'],
                            'archive_path' => $archivePath,
                            'external_url' => $resolved['external_url'] ?? null,
                        ],
                    ];
                })->values()->all(),
                'variants' => $product->variants->map(fn (ProductVariant $variant) => $variant->getAttributes())->values()->all(),
            ])
            ->values()
            ->all();
    }

    private function categorySqlDump(Category $category): string
    {
        $productIds = $category->products->pluck('id')->map(fn (int $id) => (string) $id)->implode(', ');
        $lines = [
            '-- Backup de categoria Go Center Suplementos',
            '-- Generado: '.now()->toDateTimeString(),
            '-- Categoria: '.$category->name.' ('.$category->slug.')',
            '-- Incluye solo esta categoria, sus productos, imagenes de productos y variantes.',
            'SET FOREIGN_KEY_CHECKS=0;',
        ];

        if ($productIds !== '') {
            $lines[] = "DELETE FROM `product_variants` WHERE `product_id` IN ({$productIds});";
            $lines[] = "DELETE FROM `product_images` WHERE `product_id` IN ({$productIds});";
            $lines[] = "DELETE FROM `products` WHERE `id` IN ({$productIds});";
        }

        $lines[] = 'DELETE FROM `categories` WHERE `id` = '.$category->id.';';
        $lines[] = $this->insertSql('categories', $category->getAttributes());

        foreach ($category->products as $product) {
            $lines[] = $this->insertSql('products', $product->getAttributes());

            foreach ($product->images as $image) {
                $lines[] = $this->insertSql('product_images', $image->getAttributes());
            }

            foreach ($product->variants as $variant) {
                $lines[] = $this->insertSql('product_variants', $variant->getAttributes());
            }
        }

        $lines[] = 'SET FOREIGN_KEY_CHECKS=1;';

        return implode(PHP_EOL, $lines).PHP_EOL;
    }

    private function categoryFolder(Category $category): string
    {
        return 'categorias/'.Str::slug($category->slug ?: $category->name);
    }

    private function insertSql(string $table, array $attributes): string
    {
        $columns = collect(array_keys($attributes))
            ->map(fn (string $column) => '`'.$column.'`')
            ->implode(', ');

        $values = collect(array_values($attributes))
            ->map(fn (mixed $value) => $this->sqlValue($value))
            ->implode(', ');

        return "INSERT INTO `{$table}` ({$columns}) VALUES ({$values});";
    }

    private function sqlValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return DB::getPdo()->quote((string) $value);
    }

    private function categoryBanner(Category $category): ?array
    {
        foreach ($this->categoryBannerCandidates($category) as $relativePath) {
            $absolutePath = public_path($relativePath);

            if (File::isFile($absolutePath)) {
                return $this->localImagePayload($relativePath, $absolutePath, 'files/public/'.$relativePath, 'public');
            }
        }

        return $this->scanCategoryBanner($category);
    }

    /**
     * @return array<int, string>
     */
    private function categoryBannerCandidates(Category $category): array
    {
        $extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $official = [];
        $legacy = [];

        foreach ($extensions as $extension) {
            $official[] = "assets/categories/{$category->slug}.{$extension}";
            $official[] = "assets/categories/category-{$category->slug}.{$extension}";

            // Temporary compatibility with the paths used before the asset folders were split.
            $legacy[] = "assets/gocenter/category-{$category->slug}.{$extension}";
            $legacy[] = "assets/wolfpak/{$category->slug}/category-{$category->slug}.{$extension}";
            $legacy[] = "assets/wolfpak/products/category-{$category->slug}.{$extension}";
            $legacy[] = "assets/wolfpak/mochilas/category-{$category->slug}.{$extension}";
        }

        return array_values(array_unique(array_merge($official, $legacy)));
    }

    private function scanCategoryBanner(Category $category): ?array
    {
        $assetsPath = public_path('assets');

        if (! File::isDirectory($assetsPath)) {
            return null;
        }

        $acceptedNames = collect(['jpg', 'jpeg', 'png', 'webp'])
            ->flatMap(fn (string $extension) => [
                "{$category->slug}.{$extension}",
                "category-{$category->slug}.{$extension}",
                "{$category->slug}-banner.{$extension}",
                "banner-{$category->slug}.{$extension}",
            ])
            ->all();

        foreach (File::allFiles($assetsPath) as $file) {
            if (! in_array($file->getFilename(), $acceptedNames, true)) {
                continue;
            }

            $relativePath = Str::of($file->getPathname())
                ->replace('\\', '/')
                ->after(Str::of(public_path())->replace('\\', '/').'/')
                ->toString();

            return [
                'path' => $relativePath,
                'absolute_path' => $file->getPathname(),
                'archive_path' => 'files/public/'.$relativePath,
                'included' => true,
                'source_type' => 'public',
            ];
        }

        return null;
    }

    private function resolveProductImage(ProductImage $image): array
    {
        if (str_starts_with($image->path, 'http://') || str_starts_with($image->path, 'https://')) {
            return [
                'path' => $image->path,
                'included' => false,
                'source_type' => 'external',
                'external_url' => $image->path,
            ];
        }

        if (str_starts_with($image->path, 'assets/')) {
            return $this->localImagePayload($image->path, public_path($image->path), 'files/public/'.$image->path, 'public');
        }

        $storagePath = storage_path('app/public/'.$image->path);

        if (File::isFile($storagePath)) {
            return $this->localImagePayload($image->path, $storagePath, 'files/storage/app/public/'.$image->path, 'storage');
        }

        $publicStoragePath = public_path('storage/'.$image->path);

        if (File::isFile($publicStoragePath)) {
            return $this->localImagePayload($image->path, $publicStoragePath, 'files/public/storage/'.$image->path, 'public_storage');
        }

        return [
            'path' => $image->path,
            'included' => false,
            'source_type' => 'missing',
            'missing_path' => $image->path,
        ];
    }

    private function localImagePayload(string $path, string $absolutePath, string $archivePath, string $sourceType): array
    {
        return [
            'path' => $path,
            'absolute_path' => $absolutePath,
            'archive_path' => $archivePath,
            'included' => File::isFile($absolutePath),
            'source_type' => $sourceType,
        ];
    }

    private function addLocalFile(PharData $archive, string $absolutePath, string $archivePath, array &$addedFiles): void
    {
        $archivePath = str_replace('\\', '/', $archivePath);

        if (isset($addedFiles[$archivePath]) || ! File::isFile($absolutePath)) {
            return;
        }

        $archive->addFile($absolutePath, $archivePath);
        $addedFiles[$archivePath] = true;
    }

    private function readme(): string
    {
        return <<<TXT
Backup de catalogo Go Center Suplementos

Incluye:
- manifest.json: resumen global del backup.
- media-manifest.json: listado global de archivos incluidos y referencias externas.
- categorias/{slug}/categoria.sql: SQL separado por categoria seleccionada.
- categorias/{slug}/categoria.json: datos estructurados separados por categoria.
- categorias/{slug}/media-manifest.json: medios separados por categoria.
- categorias/{slug}/files/: imagenes locales de productos y banners locales de esa categoria cuando existan.

No incluye:
- pedidos
- pagos
- clientes
- webhooks
- credenciales
- llaves API
- informacion bancaria

Nota:
Cada SQL esta pensado para restaurar una categoria especifica en una instalacion controlada. Borra y reemplaza esa categoria y sus productos relacionados, por lo que no debe importarse sin respaldo previo.
TXT;
    }
}
