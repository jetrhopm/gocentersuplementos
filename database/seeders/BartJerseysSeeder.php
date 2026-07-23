<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class BartJerseysSeeder extends Seeder
{
    public function run(): void
    {
        $catalogPath = database_path('seeders/data/bart_jerseys_catalog.json');

        if (! file_exists($catalogPath)) {
            throw new RuntimeException('No se encontro el catalogo de jerseys: '.$catalogPath);
        }

        $catalog = json_decode(file_get_contents($catalogPath), true, flags: JSON_THROW_ON_ERROR);

        $category = Category::updateOrCreate(
            ['slug' => 'jerseys'],
            [
                'name' => 'Jerseys',
                'description' => 'Jerseys de futbol para adulto y nino.',
                'active' => true,
                'sort_order' => 7,
            ]
        );

        foreach ($catalog as $productData) {
            $product = Product::updateOrCreate(
                ['slug' => $productData['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'sku' => 'BJ-'.$productData['source_id'],
                    'brand' => $productData['brand'],
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'compare_at_price' => $productData['compare_at_price'],
                    'stock' => $productData['stock'],
                    'featured' => false,
                    'active' => true,
                    'meta_title' => $productData['name'].' | '.config('app.name'),
                    'meta_description' => Str::limit($productData['description'], 150),
                ]
            );

            $product->images()->delete();

            foreach ($productData['images'] as $index => $path) {
                $product->images()->create([
                    'path' => $path,
                    'alt' => $product->name,
                    'sort_order' => $index,
                ]);
            }

            $seenVariantSkus = [];

            foreach ($productData['variants'] as $variant) {
                $seenVariantSkus[] = $variant['sku'];

                $product->variants()->updateOrCreate(
                    ['sku' => $variant['sku']],
                    [
                        'size' => $variant['size'] ?: null,
                        'color' => $variant['color'] ?: null,
                        'presentation' => $variant['presentation'] ?: null,
                        'price_modifier' => $variant['price_modifier'],
                        'stock' => $variant['stock'],
                        'active' => $variant['active'],
                    ]
                );
            }

            $product->variants()
                ->whereNotIn('sku', $seenVariantSkus)
                ->update(['active' => false, 'stock' => 0]);
        }
    }
}
