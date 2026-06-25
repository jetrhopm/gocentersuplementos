<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL');
        $adminPassword = env('ADMIN_PASSWORD');

        if (! $adminEmail || ! $adminPassword) {
            throw new RuntimeException('Define ADMIN_EMAIL y ADMIN_PASSWORD en .env antes de ejecutar los seeders.');
        }

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => env('ADMIN_NAME', 'Administrador'),
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
                'active' => true,
            ]
        );

        $categories = collect([
            ['Proteinas', 'proteinas'],
            ['Pre-entrenos', 'pre-entrenos'],
            ['Creatinas', 'creatinas'],
            ['Accesorios deportivos', 'accesorios-deportivos'],
            ['Packs Go Center', 'packs-gocenter'],
            ['Ropa deportiva hombre', 'ropa-deportiva-hombre'],
            ['Ropa deportiva mujer', 'ropa-deportiva-mujer'],
            ['Ofertas', 'ofertas'],
        ])->mapWithKeys(function (array $row, int $index) {
            return [$row[1] => Category::updateOrCreate(
                ['slug' => $row[1]],
                ['name' => $row[0], 'active' => true, 'sort_order' => $index + 1]
            )];
        });

        Category::whereIn('slug', ['proteinas', 'pre-entrenos', 'creatinas', 'accesorios-deportivos', 'ofertas'])->update(['active' => false]);

        $products = [
            [
                'category' => 'proteinas',
                'name' => 'Whey Isolate Neon 2 lb',
                'brand' => 'IronLab',
                'price' => 899,
                'compare_at_price' => 1049,
                'stock' => 18,
                'featured' => true,
                'image' => 'https://images.unsplash.com/photo-1593095948071-474c5cc2989d?auto=format&fit=crop&w=900&q=80',
                'description' => 'Proteina aislada baja en carbohidratos, ideal para recuperacion muscular despues de entrenar.',
                'variants' => [
                    ['flavor' => 'Chocolate', 'presentation' => '2 lb', 'stock' => 9],
                    ['flavor' => 'Vainilla', 'presentation' => '2 lb', 'stock' => 9],
                ],
            ],
            [
                'category' => 'proteinas',
                'name' => 'Mass Gainer Pro 5 lb',
                'brand' => 'BulkMode',
                'price' => 749,
                'stock' => 11,
                'featured' => true,
                'image' => 'https://images.unsplash.com/photo-1579758629938-03607ccdbaba?auto=format&fit=crop&w=900&q=80',
                'description' => 'Ganador de masa con mezcla de carbohidratos y proteina para etapas de volumen.',
                'variants' => [
                    ['flavor' => 'Cookies', 'presentation' => '5 lb', 'stock' => 6],
                    ['flavor' => 'Fresa', 'presentation' => '5 lb', 'stock' => 5],
                ],
            ],
            [
                'category' => 'pre-entrenos',
                'name' => 'Pre-Workout Volt X',
                'brand' => 'Pulse',
                'price' => 529,
                'compare_at_price' => 599,
                'stock' => 24,
                'featured' => true,
                'image' => 'https://images.unsplash.com/photo-1549476464-37392f717541?auto=format&fit=crop&w=900&q=80',
                'description' => 'Formula energetica para sesiones intensas con citrulina, beta alanina y cafeina.',
                'variants' => [
                    ['flavor' => 'Mora azul', 'presentation' => '30 servicios', 'stock' => 12],
                    ['flavor' => 'Sandia', 'presentation' => '30 servicios', 'stock' => 12],
                ],
            ],
            [
                'category' => 'creatinas',
                'name' => 'Creatina Monohidratada 300 g',
                'brand' => 'PureRep',
                'price' => 399,
                'stock' => 30,
                'featured' => true,
                'image' => 'https://images.unsplash.com/photo-1622484212850-eb596d769edc?auto=format&fit=crop&w=900&q=80',
                'description' => 'Creatina micronizada sin sabor para mejorar fuerza, potencia y rendimiento.',
                'variants' => [
                    ['presentation' => '300 g', 'stock' => 30],
                ],
            ],
            [
                'category' => 'accesorios-deportivos',
                'name' => 'Shaker Black Neon 700 ml',
                'brand' => 'GymTools',
                'price' => 169,
                'stock' => 40,
                'featured' => false,
                'image' => 'https://images.unsplash.com/photo-1605296867304-46d5465a13f1?auto=format&fit=crop&w=900&q=80',
                'description' => 'Shaker resistente con mezclador interno y tapa de cierre seguro.',
                'variants' => [
                    ['color' => 'Negro', 'presentation' => '700 ml', 'stock' => 20],
                    ['color' => 'Verde neon', 'presentation' => '700 ml', 'stock' => 20],
                ],
            ],
            [
                'category' => 'accesorios-deportivos',
                'name' => 'Straps de levantamiento',
                'brand' => 'GymTools',
                'price' => 219,
                'stock' => 15,
                'image' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=900&q=80',
                'description' => 'Correas para mejorar agarre en pesos muertos, remos y jalones pesados.',
            ],
            [
                'category' => 'ropa-deportiva-hombre',
                'name' => 'Playera Dry Fit Hombre',
                'brand' => 'RepWear',
                'price' => 349,
                'stock' => 22,
                'featured' => true,
                'image' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=900&q=80',
                'description' => 'Playera transpirable de ajuste atletico para entrenamiento diario.',
                'variants' => [
                    ['size' => 'S', 'color' => 'Negro', 'stock' => 5],
                    ['size' => 'M', 'color' => 'Negro', 'stock' => 8],
                    ['size' => 'L', 'color' => 'Gris', 'stock' => 9],
                ],
            ],
            [
                'category' => 'ropa-deportiva-hombre',
                'name' => 'Short Training Hombre',
                'brand' => 'RepWear',
                'price' => 429,
                'stock' => 16,
                'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=900&q=80',
                'description' => 'Short ligero con bolsas laterales y libertad de movimiento.',
                'variants' => [
                    ['size' => 'M', 'color' => 'Negro', 'stock' => 8],
                    ['size' => 'L', 'color' => 'Verde', 'stock' => 8],
                ],
            ],
            [
                'category' => 'ropa-deportiva-mujer',
                'name' => 'Leggings High Rise Mujer',
                'brand' => 'FlexFit',
                'price' => 599,
                'compare_at_price' => 699,
                'stock' => 19,
                'featured' => true,
                'image' => 'https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=900&q=80',
                'description' => 'Leggings de compresion media con cintura alta y tela suave.',
                'variants' => [
                    ['size' => 'S', 'color' => 'Negro', 'stock' => 7],
                    ['size' => 'M', 'color' => 'Vino', 'stock' => 6],
                    ['size' => 'L', 'color' => 'Gris', 'stock' => 6],
                ],
            ],
            [
                'category' => 'ropa-deportiva-mujer',
                'name' => 'Top Deportivo Mujer',
                'brand' => 'FlexFit',
                'price' => 329,
                'stock' => 13,
                'image' => 'https://images.unsplash.com/photo-1540206276207-3af25c08abc4?auto=format&fit=crop&w=900&q=80',
                'description' => 'Top con soporte medio para pesas, caminadora y clases funcionales.',
                'variants' => [
                    ['size' => 'S', 'color' => 'Negro', 'stock' => 5],
                    ['size' => 'M', 'color' => 'Blanco', 'stock' => 4],
                    ['size' => 'L', 'color' => 'Rosa', 'stock' => 4],
                ],
            ],
            [
                'category' => 'ofertas',
                'name' => 'Combo Definicion',
                'brand' => 'IronLab',
                'price' => 1199,
                'compare_at_price' => 1499,
                'stock' => 7,
                'featured' => true,
                'image' => 'https://images.unsplash.com/photo-1599058917212-d750089bc07e?auto=format&fit=crop&w=900&q=80',
                'description' => 'Combo de whey, creatina y shaker para iniciar un stack de definicion.',
            ],
            [
                'category' => 'ofertas',
                'name' => 'Kit Fuerza Total',
                'brand' => 'PureRep',
                'price' => 999,
                'compare_at_price' => 1299,
                'stock' => 4,
                'image' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?auto=format&fit=crop&w=900&q=80',
                'description' => 'Creatina, straps y pre-entreno para ciclos de fuerza.',
            ],
        ];

        $products = array_values(array_filter($products, fn (array $product) => in_array($product['category'], ['ropa-deportiva-hombre', 'ropa-deportiva-mujer'], true)));

        $products = array_merge($products, [
            [
                'category' => 'packs-gocenter',
                'name' => 'Pack Isolate Inlabs 5 + Creatina 200',
                'brand' => 'Inlabs',
                'price' => 3790,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/pack-isolate-inlabs-3790.jpg',
                'description' => 'Pack Isolate Inlabs. Incluye 5 Isolate Inlabs 5LB y 1 Creatina Inlabs 200 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Pack Whey Inlabs 5 + Creatina 200',
                'brand' => 'Inlabs',
                'price' => 3290,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/pack-whey-inlabs-3290.jpg',
                'description' => 'Pack Whey Inlabs. Incluye 5 Whey Inlabs 5LB y 1 Creatina Inlabs 200 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Pack Isolate Inlabs 4 + Creatina 200',
                'brand' => 'Inlabs',
                'price' => 3160,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/pack-isolate-inlabs-3160.jpg',
                'description' => 'Pack Isolate Inlabs. Incluye 4 Isolate Inlabs 5LB y 1 Creatina Inlabs 200 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Pack Whey Inlabs 4 + Creatina 200',
                'brand' => 'Inlabs',
                'price' => 2760,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/pack-whey-inlabs-2760.jpg',
                'description' => 'Pack Whey Inlabs. Incluye 4 Whey Inlabs 5LB y 1 Creatina Inlabs 200 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Pack Performance Isolate 3 + Creatina 60',
                'brand' => 'Inlabs',
                'price' => 2370,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/pack-performance-2370.jpg',
                'description' => 'Pack Performance. Incluye 3 Isolate Inlabs 5LB y 1 Creatina Inlabs 60 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Pack Whey Inlabs 3 + Creatina 60',
                'brand' => 'Inlabs',
                'price' => 2070,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/pack-whey-inlabs-2070.jpg',
                'description' => 'Pack Whey Inlabs. Incluye 3 Whey Inlabs 5LB y 1 Creatina Inlabs 60 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Pack Isolate Inlabs 2 + Creatina 60',
                'brand' => 'Inlabs',
                'price' => 1650,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/pack-isolate-inlabs-1650.jpg',
                'description' => 'Pack Isolate Inlabs. Incluye 2 Isolate Inlabs 5LB y 1 Creatina Inlabs 60 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Pack Whey Inlabs 2 + Creatina 60',
                'brand' => 'Inlabs',
                'price' => 1450,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/pack-whey-inlabs-1450.jpg',
                'description' => 'Pack Whey Inlabs. Incluye 2 Whey Inlabs 5LB y 1 Creatina Inlabs 60 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Pack Mutant Mass + Creatina Inlabs',
                'brand' => 'Go Center',
                'price' => 1250,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/pack-mutant-mass-creatina-1250.jpg',
                'description' => 'Incluye 2 Mutant Mass Extreme 5LB y 1 Creatina Inlabs 100 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Promo Completa',
                'brand' => 'Go Center',
                'price' => 1390,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/promo-completa-1390.jpg',
                'description' => 'Incluye 1 BCAA Inlabs 30 servicios, 1 CBum Essential 30 servicios, 1 Creatina Isopure 100 servicios y 1 Omega 3 Dragon 60 softgel. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Pack Entrenamiento Completo Morado',
                'brand' => 'Go Center',
                'price' => 1520,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/pack-entrenamiento-completo-1520.jpg',
                'description' => 'Incluye 1 Whey Inlabs 5LB, 1 Venom Essential 30 servicios, 1 Breach 30 servicios y 1 Creatina Inlabs 60 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Super Promo Azul',
                'brand' => 'Go Center',
                'price' => 2260,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/super-promo-2260.jpg',
                'description' => 'Incluye 1 Creatina GAT 200 servicios, 1 Omega Inlabs 90 caps, 1 Isolate Inlabs 5LB y 1 Pre Ghost 30 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Pack Entrenamiento Completo Amarillo',
                'brand' => 'Go Center',
                'price' => 1290,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/pack-entrenamiento-completo-1290.jpg',
                'description' => 'Incluye 1 CBum Essential 30 servicios, 1 Venom Essential 30 servicios y 1 Creatina BPI 120 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Combo Completo',
                'brand' => 'Go Center',
                'price' => 1630,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/combo-completo-1630.jpg',
                'description' => 'Incluye 1 Creatina BSN 60 servicios, 1 Cafeina Mutant 240 tabletas, 1 Whey Inlabs 5LB, 1 Total War 30 servicios y 1 Amino Inlabs 30 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Super Promo Verde',
                'brand' => 'Go Center',
                'price' => 990,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/super-promo-990.jpg',
                'description' => 'Incluye 1 BCAA Hardcore 30 servicios, 1 Total War Reloaded 30 servicios y 1 Creatina BSN 60 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Promocion Morada',
                'brand' => 'Go Center',
                'price' => 2370,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/promocion-2370.jpg',
                'description' => 'Incluye CBum Essential 30 servicios, Mr Veinz 40 servicios, Nitro Tech Whey Gold 5LB y Amino Energy 30 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Combo Entrenamiento',
                'brand' => 'Go Center',
                'price' => 1490,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/combo-entrenamiento-1490.jpg',
                'description' => 'Incluye Xtend BCAA 30 servicios, Whey Inlabs 5LB, Creatina Inlabs 200 servicios y Psychotic 35 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Super Pack',
                'brand' => 'Go Center',
                'price' => 2390,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/super-pack-2390.jpg',
                'description' => 'Incluye Creatina Creapure Ghost 50 servicios, Glycerol Nitraflex 32 servicios, Whey Inlabs 5LB, Venom Essential 30 servicios, Mr Veinz 40 servicios y regalo Creatina Inlabs 100 servicios. Costo de envio no incluido.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Combo Completo Hydro',
                'brand' => 'Go Center',
                'price' => 2370,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/combo-completo-hydro-2370.jpg',
                'description' => 'Incluye Psychotic 35 servicios, Omega Inlabs 90 capsulas, Hydro Whey Inlabs 5LB, Shaker Insane Labz, Creatina Inlabs 200 servicios sin sabor, Best BCAA 30 servicios, regalo Amino Inlabs 30 servicios y regalo Multi Inlabs 50 servicios.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Paquete Completo',
                'brand' => 'Go Center',
                'price' => 2190,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/paquete-completo-2190.jpg',
                'description' => 'Incluye Creatina Meta 100 servicios, Cafeina Mutant 240 tabletas, Whey Inlabs 5LB, Shaker Birdman, Omega 3 Blife 90 capsulas, Psychotic Black 35 servicios, regalo Creatina Inlabs 60 servicios y regalo Beta Alanina Inlabs 100 servicios.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Oferta Flash Amino Inlabs 5 Piezas',
                'brand' => 'Go Center',
                'price' => 990,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/oferta-flash-amino-inlabs-990.jpg',
                'description' => 'Oferta flash con 5 Amino Inlabs de 30 servicios cada uno. Ideal para recuperacion muscular y entrenamiento constante.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Super Combo',
                'brand' => 'Go Center',
                'price' => 3090,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/super-combo-3090.jpg',
                'description' => 'Incluye Creatina Dragon 60 servicios, ZMA GAT 90 capsulas, Shaker Birdman, Gold Standard 5LB, Carnitina Ronnie 4000 32 servicios, Maniak 30 servicios, regalo Nitraflex Sport 20 servicios y regalo Amino Inlabs 30 servicios.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Combo Completo Azul',
                'brand' => 'Go Center',
                'price' => 1880,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/combo-completo-azul-1880.jpg',
                'description' => 'Incluye Citrulina Dragon 60 servicios, Omega 3 Salmon Oil BLife 90 capsulas, Whey Inlabs 5LB, Shaker Insane, Psychotic 35 servicios y regalo Multi Inlabs 50 servicios.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Combo Completo Dorado',
                'brand' => 'Go Center',
                'price' => 2250,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/combo-completo-dorado-2250.jpg',
                'description' => 'Incluye Omega Inlabs 90 capsulas, Amino X 30 servicios, Isolate Inlabs 5LB, Shaker Spider, Psychotic Black 35 servicios, Cafeina Mutant 240 tabletas, regalo Creatina Inlabs 60 servicios y regalo Multi Inlabs 50 servicios.',
            ],
            [
                'category' => 'packs-gocenter',
                'name' => 'Mega Combo',
                'brand' => 'Go Center',
                'price' => 2330,
                'stock' => 10,
                'featured' => true,
                'image' => 'assets/gocenter/products/mega-combo-2330.jpg',
                'description' => 'Incluye Omega 3 GAT 90 capsulas, Amino Energy 30 servicios, Gold Standard 2LB, Shaker Insane Labz, Glutamina Mutant 69 servicios, Psychotic SAW 30 servicios, regalo Amino Inlabs 30 servicios y regalo Creatina Inlabs 60 servicios.',
            ],
        ]);

        foreach ($products as $productData) {
            $category = $categories[$productData['category']];
            $variants = $productData['variants'] ?? [];
            $image = $productData['image'];
            unset($productData['category'], $productData['variants'], $productData['image']);

            $product = Product::updateOrCreate(
                ['slug' => Str::slug($productData['name'])],
                array_merge($productData, [
                    'category_id' => $category->id,
                    'active' => true,
                    'sku' => 'SKU-'.Str::upper(Str::slug($productData['name'], '-')),
                    'meta_title' => $productData['name'].' | '.config('app.name'),
                    'meta_description' => Str::limit($productData['description'], 150),
                ])
            );

            $product->images()->updateOrCreate(
                ['sort_order' => 0],
                ['path' => $image, 'alt' => $product->name]
            );

            foreach ($variants as $index => $variant) {
                $product->variants()->updateOrCreate(
                    ['sku' => $product->sku.'-V'.($index + 1)],
                    array_merge([
                        'price_modifier' => 0,
                        'stock' => $product->stock,
                        'active' => true,
                    ], $variant)
                );
            }
        }

        Coupon::updateOrCreate(
            ['code' => 'GYM10'],
            ['type' => 'percent', 'value' => 10, 'minimum_total' => 500, 'max_uses' => 100, 'active' => true]
        );

        foreach ([
            'maintenance_mode' => '0',
            'store_email' => 'ventas@local.test',
        ] as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => 'string', 'is_public' => false]);
        }
    }
}
