<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WolfpakMochilasSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::updateOrCreate(
            ['slug' => 'mochilas'],
            ['name' => 'Mochilas', 'active' => true, 'sort_order' => 6]
        );

        $products = [
            [
                'name' => '30L Perfect Duffle Bag Superman 2025',
                'price' => 2800,
                'compare_at_price' => 3500,
                'images' => [
                    'assets/wolfpak/products/30l-perfect-duffle-bag-superman-2025-1.png',
                    'assets/wolfpak/products/30l-perfect-duffle-bag-superman-2025-2.jpg',
                    'assets/wolfpak/products/30l-perfect-duffle-bag-superman-2025-3.jpg',
                ],
                'description' => 'Maleta duffle Wolfpak de 30L edicion Superman, hecha con material Oxford impermeable, cierres YKK, base rigida, apertura completa, compartimentos para laptop, calzado y accesorios. Incluye parches removibles y correa ajustable para gimnasio o viaje.',
            ],
            [
                'name' => '9L Backpack Mini Superman',
                'price' => 1540,
                'compare_at_price' => 1930,
                'images' => [
                    'assets/wolfpak/products/9l-backpack-mini-superman-1.png',
                    'assets/wolfpak/products/9l-backpack-mini-superman-2.jpg',
                    'assets/wolfpak/products/9l-backpack-mini-superman-3.jpg',
                ],
                'description' => 'Mini backpack Wolfpak de 9L edicion Superman. Mochila compacta para oficina, gimnasio, hiking o uso diario, con superficie Oxford 900D impermeable, apertura 180 grados, cierre YKK resistente, espacio Hook & Loop para parches y compartimento interior para telefono o tablet pequena.',
            ],
            [
                'name' => '9L Tactical Sling Bag Batman',
                'price' => 1680,
                'compare_at_price' => 2100,
                'images' => [
                    'assets/wolfpak/products/9l-tactical-sling-bag-batman-1.png',
                    'assets/wolfpak/products/9l-tactical-sling-bag-batman-2.jpg',
                    'assets/wolfpak/products/9l-tactical-sling-bag-batman-3.jpg',
                ],
                'description' => 'Sling bag tactica Wolfpak Batman de 9L. Bolsa cruzada ligera con nylon resistente al clima, bolsillos internos, cierre dual YKK, sistema MOLLE frontal, bolsillo posterior acolchado y correa ajustable para usar al pecho, espalda u hombro.',
            ],
            [
                'name' => '30L Perfect Duffle Bag Batman',
                'price' => 2800,
                'compare_at_price' => 3500,
                'images' => [
                    'assets/wolfpak/products/30l-perfect-duffle-bag-batman-1.png',
                    'assets/wolfpak/products/30l-perfect-duffle-bag-batman-2.jpg',
                    'assets/wolfpak/products/30l-perfect-duffle-bag-batman-3.jpg',
                ],
                'description' => 'Maleta duffle Wolfpak Batman de 30L para viaje o gimnasio, con Oxford impermeable, cierres YKK, base rigida, apertura completa, bolsillos multiples, porta laptop, separador para calzado, manga trasera para trolley y parches removibles incluidos.',
            ],
            [
                'name' => '35L Batman Meal Prep Management',
                'price' => 3390,
                'compare_at_price' => 4240,
                'images' => [
                    'assets/wolfpak/products/35l-batman-meal-prep-management-1.png',
                    'assets/wolfpak/products/35l-batman-meal-prep-management-2.png',
                    'assets/wolfpak/products/35l-batman-meal-prep-management-3.png',
                ],
                'description' => 'Backpack Wolfpak Batman Meal Prep de 35L con compartimento cooler integrado, contenedores de comida, bolsa de hielo, utensilios, bolsillos amplios, porta laptop, doble porta shaker y superficie Oxford 900D impermeable. Ideal para gimnasio, trabajo y preparacion de comidas.',
            ],
            [
                'name' => '4L Mini Tactical Sling Bag Batman',
                'price' => 1540,
                'compare_at_price' => 1930,
                'images' => [
                    'assets/wolfpak/products/4l-mini-tactical-sling-bag-batman-1.png',
                    'assets/wolfpak/products/4l-mini-tactical-sling-bag-batman-2.jpg',
                    'assets/wolfpak/products/4l-mini-tactical-sling-bag-batman-3.jpg',
                ],
                'description' => 'Mini sling bag tactica Wolfpak Batman de 4L. Compacta, ligera y resistente al clima, con organizadores internos, cierre dual YKK, sistema MOLLE, bolsillo acolchado posterior y correa ajustable para llevar tus esenciales sin cargar una mochila grande.',
            ],
            [
                'name' => '15L Backpack Batman',
                'price' => 2380,
                'compare_at_price' => 2980,
                'images' => [
                    'assets/wolfpak/products/15l-backpack-batman-1.png',
                    'assets/wolfpak/products/15l-backpack-batman-2.jpg',
                    'assets/wolfpak/products/15l-backpack-batman-3.jpg',
                ],
                'description' => 'Backpack Wolfpak Batman de 15L para uso diario, oficina, gimnasio o escuela. Cuenta con trolley sleeve, superficie Oxford impermeable, apertura amplia, cierres YKK, espacio para parches removibles y compartimentos para organizar accesorios.',
            ],
            [
                'name' => '9L Backpack Mini Batman',
                'price' => 1540,
                'compare_at_price' => 1930,
                'images' => [
                    'assets/wolfpak/products/9l-backpack-mini-batman-1.png',
                    'assets/wolfpak/products/9l-backpack-mini-batman-2.jpg',
                    'assets/wolfpak/products/9l-backpack-mini-batman-3.jpg',
                ],
                'description' => 'Mini backpack Wolfpak Batman de 9L. Mochila compacta con Oxford 900D impermeable, apertura 180 grados, cierres YKK, espacio Hook & Loop para parches y compartimento interior para telefono o tablet pequena.',
            ],
            [
                'name' => '9L Backpack Mini Retro Batman',
                'price' => 1930,
                'compare_at_price' => null,
                'images' => [
                    'assets/wolfpak/products/9l-backpack-mini-retro-batman-1.png',
                    'assets/wolfpak/products/9l-backpack-mini-retro-batman-2.jpg',
                    'assets/wolfpak/products/9l-backpack-mini-retro-batman-3.jpg',
                ],
                'description' => 'Mini backpack Wolfpak Retro Batman de 9L con diseno compacto para uso diario, oficina, gimnasio o salidas. Integra superficie Oxford 900D impermeable, apertura amplia, cierres YKK, espacio para parches y compartimento interior protector.',
            ],
            [
                'name' => '30L Perfect Duffle Bag DC Villains',
                'price' => 2800,
                'compare_at_price' => 3500,
                'images' => [
                    'assets/wolfpak/products/30l-perfect-duffle-bag-dc-villains-1.png',
                    'assets/wolfpak/products/30l-perfect-duffle-bag-dc-villains-2.jpg',
                    'assets/wolfpak/products/30l-perfect-duffle-bag-dc-villains-3.jpg',
                ],
                'description' => 'Maleta duffle Wolfpak DC Villains de 30L, resistente para viaje y gimnasio. Incluye Oxford impermeable, cierres YKK, base rigida, apertura completa, multiples bolsillos, area para laptop, compartimento de calzado y parches removibles.',
            ],
            [
                'name' => '9L Backpack Mini DC Villains',
                'price' => 1540,
                'compare_at_price' => 1930,
                'images' => [
                    'assets/wolfpak/products/9l-backpack-mini-dc-villains-1.png',
                    'assets/wolfpak/products/9l-backpack-mini-dc-villains-2.jpg',
                    'assets/wolfpak/products/9l-backpack-mini-dc-villains-3.jpg',
                ],
                'description' => 'Mini backpack Wolfpak DC Villains de 9L para uso diario, gimnasio o viajes cortos. Fabricada con Oxford 900D impermeable, apertura 180 grados, cierres YKK resistentes, espacio para parches y compartimento interior para telefono o tablet.',
            ],
            [
                'name' => '9L Backpack Mini The Flash',
                'price' => 1350,
                'compare_at_price' => 1930,
                'images' => [
                    'assets/wolfpak/products/9l-backpack-mini-the-flash-1.png',
                    'assets/wolfpak/products/9l-backpack-mini-the-flash-2.jpg',
                    'assets/wolfpak/products/9l-backpack-mini-the-flash-3.jpg',
                    'assets/wolfpak/products/9l-backpack-mini-the-flash-4.jpg',
                ],
                'description' => 'Mini backpack Wolfpak The Flash de 9L para uso diario, gimnasio u oficina. Compacta, impermeable en Oxford 900D, apertura de 180 grados, cierres YKK, compartimento interior para telefono o tablet pequena y parches removibles inspirados en The Flash.',
            ],
            [
                'name' => '30L Perfect Duffle Bag The Flash',
                'price' => 2450,
                'compare_at_price' => 3500,
                'images' => [
                    'assets/wolfpak/products/30l-perfect-duffle-bag-the-flash-1.png',
                    'assets/wolfpak/products/30l-perfect-duffle-bag-the-flash-2.jpg',
                    'assets/wolfpak/products/30l-perfect-duffle-bag-the-flash-3.jpg',
                    'assets/wolfpak/products/30l-perfect-duffle-bag-the-flash-4.jpg',
                ],
                'description' => 'Maleta duffle Wolfpak The Flash de 30L para viaje y gimnasio. Construida en Oxford impermeable 1000D con cierres YKK, base rigida, apertura completa, compartimento para laptop, espacio para calzado, porta vasos, correa ajustable y parches removibles.',
            ],
            [
                'name' => '4L Mini Tactical Sling Bag Rock & Reaper',
                'price' => 1470,
                'compare_at_price' => 1840,
                'images' => [
                    'assets/wolfpak/products/4l-mini-tactical-sling-bag-rock-reaper-1.jpg',
                    'assets/wolfpak/products/4l-mini-tactical-sling-bag-rock-reaper-2.jpg',
                    'assets/wolfpak/products/4l-mini-tactical-sling-bag-rock-reaper-3.jpg',
                    'assets/wolfpak/products/4l-mini-tactical-sling-bag-rock-reaper-4.jpg',
                ],
                'description' => 'Mini sling bag Wolfpak Rock & Reaper de 4L, ligera y tactica para cargar esenciales. Incluye nylon resistente al clima, bolsillos organizadores, cierre dual YKK, sistema MOLLE frontal, bolsillo posterior acolchado y correa ajustable para pecho, hombro o espalda.',
            ],
            [
                'name' => '9L Backpack Mini Mortal Kombat',
                'price' => 1540,
                'compare_at_price' => 1930,
                'images' => [
                    'assets/wolfpak/products/9l-backpack-mini-mortal-kombat-1.jpg',
                    'assets/wolfpak/products/9l-backpack-mini-mortal-kombat-2.jpg',
                    'assets/wolfpak/products/9l-backpack-mini-mortal-kombat-3.jpg',
                    'assets/wolfpak/products/9l-backpack-mini-mortal-kombat-4.jpg',
                ],
                'description' => 'Mini backpack Wolfpak Mortal Kombat de 9L, compacta y resistente para uso diario. Fabricada en Oxford 900D impermeable, apertura de 180 grados, cierres YKK, espacio para parches removibles y bolsillo interior para telefono o tablet pequena.',
            ],
            [
                'name' => 'The Champion of Grayskull Bundle',
                'price' => 5430,
                'compare_at_price' => null,
                'images' => [
                    'assets/wolfpak/products/the-champion-of-grayskull-bundle-1.jpg',
                    'assets/wolfpak/products/the-champion-of-grayskull-bundle-2.jpg',
                    'assets/wolfpak/products/the-champion-of-grayskull-bundle-3.jpg',
                    'assets/wolfpak/products/the-champion-of-grayskull-bundle-4.jpg',
                ],
                'description' => 'Bundle Wolfpak Masters of The Universe He-Man con backpack de 35L y sling tactico de 9L. Paquete pensado para entrenamiento, viaje y uso diario, con gran capacidad, compartimentos funcionales, parches Hook & Loop y kit de challenge coin.',
            ],
            [
                'name' => '9L Backpack Mini Dead Men Tell No Tales',
                'price' => 1470,
                'compare_at_price' => 1840,
                'images' => [
                    'assets/wolfpak/products/9l-backpack-mini-dead-men-tell-no-tales-1.jpg',
                    'assets/wolfpak/products/9l-backpack-mini-dead-men-tell-no-tales-2.jpg',
                    'assets/wolfpak/products/9l-backpack-mini-dead-men-tell-no-tales-3.jpg',
                    'assets/wolfpak/products/9l-backpack-mini-dead-men-tell-no-tales-4.jpg',
                ],
                'description' => 'Mini backpack Wolfpak Dead Men Tell No Tales de 9L con estilo pirata. Mochila compacta para oficina, gimnasio o viajes cortos, en Oxford 900D impermeable, apertura 180 grados, cierres YKK, parches removibles y compartimento interior protector.',
            ],
            [
                'name' => '45L Backpack Mortal Kombat',
                'price' => 2800,
                'compare_at_price' => 3500,
                'images' => [
                    'assets/wolfpak/products/45l-backpack-mortal-kombat-1.jpg',
                    'assets/wolfpak/products/45l-backpack-mortal-kombat-2.jpg',
                    'assets/wolfpak/products/45l-backpack-mortal-kombat-3.jpg',
                    'assets/wolfpak/products/45l-backpack-mortal-kombat-4.jpg',
                ],
                'description' => 'Backpack Wolfpak Mortal Kombat de 45L para entrenamiento, viaje o uso diario pesado. Cuenta con Oxford 900D impermeable, doble porta shaker, compartimento para laptop, apertura 180 grados, cierres YKK, parches removibles y correas resistentes.',
            ],
            [
                'name' => '9L Tactical Sling Bag Buried In Ink',
                'price' => 1650,
                'compare_at_price' => 2070,
                'images' => [
                    'assets/wolfpak/products/9l-tactical-sling-bag-buried-in-ink-1.jpg',
                    'assets/wolfpak/products/9l-tactical-sling-bag-buried-in-ink-2.jpg',
                    'assets/wolfpak/products/9l-tactical-sling-bag-buried-in-ink-3.jpg',
                    'assets/wolfpak/products/9l-tactical-sling-bag-buried-in-ink-4.jpg',
                ],
                'description' => 'Sling bag tactica Wolfpak Buried In Ink de 9L. Bolsa cruzada resistente al clima con organizadores internos, cierre dual YKK, sistema MOLLE, bolsillo posterior acolchado, parche removible y correa ajustable para pecho, hombro o espalda.',
            ],
            [
                'name' => '9L Tactical Sling Bag Until Forever',
                'price' => 1650,
                'compare_at_price' => 2070,
                'images' => [
                    'assets/wolfpak/products/9l-tactical-sling-bag-until-forever-1.jpg',
                    'assets/wolfpak/products/9l-tactical-sling-bag-until-forever-2.jpg',
                    'assets/wolfpak/products/9l-tactical-sling-bag-until-forever-3.jpg',
                    'assets/wolfpak/products/9l-tactical-sling-bag-until-forever-4.jpg',
                ],
                'description' => 'Sling bag tactica Wolfpak Until Forever de 9L, compacta y funcional para tus esenciales. Tiene nylon resistente al clima, bolsillos internos, cierre dual YKK, sistema MOLLE, bolsillo posterior acolchado, parche removible y correa ajustable.',
            ],
            [
                'name' => '15L Backpack Harley Quinn',
                'price' => 2080,
                'compare_at_price' => 2980,
                'images' => [
                    'assets/wolfpak/products/15l-backpack-harley-quinn-1.jpg',
                    'assets/wolfpak/products/15l-backpack-harley-quinn-2.jpg',
                    'assets/wolfpak/products/15l-backpack-harley-quinn-3.jpg',
                    'assets/wolfpak/products/15l-backpack-harley-quinn-4.jpg',
                ],
                'description' => 'Backpack Wolfpak Harley Quinn de 15L para escuela, oficina, gimnasio o viaje. Incluye trolley sleeve, tres compartimentos, porta vasos, pouch interior para laptop, apertura 180 grados, cierres YKK, espalda acolchada y parches removibles.',
            ],
            [
                'name' => '30L Perfect Duffle Bag IT Chapter 2',
                'price' => 2980,
                'compare_at_price' => 3500,
                'images' => [
                    'assets/wolfpak/products/30l-perfect-duffle-bag-it-chapter-2-1.jpg',
                    'assets/wolfpak/products/30l-perfect-duffle-bag-it-chapter-2-2.jpg',
                    'assets/wolfpak/products/30l-perfect-duffle-bag-it-chapter-2-3.jpg',
                    'assets/wolfpak/products/30l-perfect-duffle-bag-it-chapter-2-4.jpg',
                ],
                'description' => 'Maleta duffle Wolfpak IT Chapter 2 de 30L para viaje o gimnasio. Fabricada en Oxford impermeable 1000D con cierres YKK, base rigida, apertura completa, porta laptop, compartimento para calzado, correa ajustable y parches removibles.',
            ],
            [
                'name' => '9L Backpack Mini High Stakes',
                'price' => 1290,
                'compare_at_price' => 1840,
                'images' => [
                    'assets/wolfpak/products/9l-backpack-mini-high-stakes-1.png',
                    'assets/wolfpak/products/9l-backpack-mini-high-stakes-2.jpg',
                    'assets/wolfpak/products/9l-backpack-mini-high-stakes-3.jpg',
                    'assets/wolfpak/products/9l-backpack-mini-high-stakes-4.jpg',
                ],
                'description' => 'Mini backpack Wolfpak High Stakes de 9L con estilo casino. Mochila compacta para uso diario, oficina o gimnasio, fabricada en Oxford 900D impermeable, apertura 180 grados, cierres YKK, parches removibles y bolsillo interior para telefono o tablet pequena.',
            ],
            [
                'name' => '35L Backpack Nightmare on Elm Street',
                'price' => 2830,
                'compare_at_price' => 3330,
                'images' => [
                    'assets/wolfpak/products/35l-backpack-nightmare-on-elm-street-1.jpg',
                    'assets/wolfpak/products/35l-backpack-nightmare-on-elm-street-2.jpg',
                    'assets/wolfpak/products/35l-backpack-nightmare-on-elm-street-3.jpg',
                    'assets/wolfpak/products/35l-backpack-nightmare-on-elm-street-4.jpg',
                ],
                'description' => 'Backpack Wolfpak Nightmare on Elm Street de 35L para gimnasio, viaje o uso diario. Incluye Oxford impermeable, apertura 180 grados, cierres YKK, doble porta shaker, compartimento para laptop, parches removibles y correas resistentes.',
            ],
            [
                'name' => '35L Backpack Hot Wheels Racing',
                'price' => 2330,
                'compare_at_price' => 3330,
                'images' => [
                    'assets/wolfpak/products/35l-backpack-hot-wheels-racing-1.jpg',
                    'assets/wolfpak/products/35l-backpack-hot-wheels-racing-2.jpg',
                    'assets/wolfpak/products/35l-backpack-hot-wheels-racing-3.jpg',
                    'assets/wolfpak/products/35l-backpack-hot-wheels-racing-4.jpg',
                ],
                'description' => 'Backpack Wolfpak Hot Wheels Racing de 35L con look deportivo. Pensada para entrenamiento, escuela o viaje, con Oxford 900D impermeable, cierres YKK, apertura amplia, doble porta shaker, compartimento para laptop y parches removibles.',
            ],
            [
                'name' => '25L Backpack High Stakes',
                'price' => 1960,
                'compare_at_price' => 2800,
                'images' => [
                    'assets/wolfpak/products/25l-backpack-high-stakes-1.png',
                    'assets/wolfpak/products/25l-backpack-high-stakes-2.jpg',
                    'assets/wolfpak/products/25l-backpack-high-stakes-3.jpg',
                    'assets/wolfpak/products/25l-backpack-high-stakes-4.jpg',
                ],
                'description' => 'Backpack tactica Wolfpak High Stakes de 25L para oficina, gimnasio y uso diario. Cuenta con Oxford 900D impermeable, cierres YKK, apertura 180 grados, doble porta shaker, compartimento para laptop, parches removibles y correas de carga.',
            ],
            [
                'name' => '45L Backpack Nightmare on Elm Street',
                'price' => 2980,
                'compare_at_price' => 3500,
                'images' => [
                    'assets/wolfpak/products/45l-backpack-nightmare-on-elm-street-1.jpg',
                    'assets/wolfpak/products/45l-backpack-nightmare-on-elm-street-2.jpg',
                    'assets/wolfpak/products/45l-backpack-nightmare-on-elm-street-3.jpg',
                    'assets/wolfpak/products/45l-backpack-nightmare-on-elm-street-4.jpg',
                ],
                'description' => 'Backpack Wolfpak Nightmare on Elm Street de 45L para quienes necesitan mayor capacidad. Incluye Oxford 900D impermeable, apertura 180 grados, cierres YKK, doble porta shaker, compartimento para laptop, parches removibles y correas reforzadas.',
            ],
        ];

        foreach ($products as $productData) {
            $images = $productData['images'];

            $product = Product::updateOrCreate(
                ['slug' => Str::slug($productData['name'])],
                [
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'brand' => 'Wolfpak',
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'compare_at_price' => $productData['compare_at_price'],
                    'stock' => 8,
                    'featured' => false,
                    'active' => true,
                ]
            );

            foreach ($images as $index => $path) {
                $product->images()->updateOrCreate(
                    ['sort_order' => $index],
                    ['path' => $path, 'alt' => $product->name]
                );
            }
        }
    }
}
