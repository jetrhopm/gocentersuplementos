<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GoCenterNewProductsSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('slug', 'packs-gocenter')->firstOrFail();

        DB::transaction(function () use ($category) {
            Product::where('category_id', $category->id)->get()->each->delete();

            foreach ($this->products() as $item) {
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $item['name'],
                    'slug' => $item['slug'],
                    'sku' => 'GC-'.Str::upper(Str::slug($item['slug'], '-')),
                    'brand' => 'Go Center',
                    'description' => $this->description($item),
                    'price' => $item['price'],
                    'compare_at_price' => null,
                    'stock' => 10,
                    'featured' => true,
                    'active' => true,
                    'meta_title' => $item['name'].' | Go Center Suplementos',
                    'meta_description' => $this->description($item),
                ]);

                $product->images()->create([
                    'path' => 'assets/gocenter/products/'.$item['image'],
                    'alt' => $item['name'],
                    'sort_order' => 0,
                ]);
            }
        });
    }

    private function description(array $item): string
    {
        return 'Pack promocional Go Center. Incluye: '.$item['includes'].'. Costo de envio no incluido.';
    }

    private function products(): array
    {
        return [
            ['name' => 'Promocion Nitro Tech, Veinz y Amino Energy', 'slug' => 'promocion-nitro-tech-veinz-amino-energy-1970', 'price' => 1970, 'image' => 'promocion-nitro-tech-veinz-amino-energy-1970.jpg', 'includes' => 'Nitro Tech, Mr Veinz y Amino Energy'],
            ['name' => 'Combo Favorito Mutant Mass', 'slug' => 'combo-favorito-mutant-mass-1280', 'price' => 1280, 'image' => 'combo-favorito-mutant-mass-1280.jpg', 'includes' => 'creatina GAT, Mutant Mass Extreme y Psychotic Black; regalo BCAAS'],
            ['name' => 'Combo Completo Creatina, Whey y Amino', 'slug' => 'combo-completo-creatina-whey-amino-1390', 'price' => 1390, 'image' => 'combo-completo-creatina-whey-amino-1390.jpg', 'includes' => 'creatina, whey protein, cafeina, Total War y Amino'],
            ['name' => 'Pack Entrenamiento Completo', 'slug' => 'pack-entrenamiento-completo-1090', 'price' => 1090, 'image' => 'pack-entrenamiento-completo-1090.jpg', 'includes' => 'pre workout, Venom Essential y creatina BPI'],
            ['name' => 'Super Promo Azul', 'slug' => 'super-promo-azul-1860', 'price' => 1860, 'image' => 'super-promo-azul-1860.jpg', 'includes' => 'creatina GAT, omega, Isolate Inlabs y Pre Ghost'],
            ['name' => 'Pack Entrenamiento Completo Morado', 'slug' => 'pack-entrenamiento-completo-morado-1290', 'price' => 1290, 'image' => 'pack-entrenamiento-completo-morado-1290.jpg', 'includes' => 'Whey Inlabs, Venom Essential, Breach y creatina Inlabs'],
            ['name' => 'Promo Completa BCAAS, Pre e Isopure', 'slug' => 'promo-completa-bcaas-pre-isopure-1090', 'price' => 1090, 'image' => 'promo-completa-bcaas-pre-isopure-1090.jpg', 'includes' => 'BCAAS, essential pre workout, Isopure, omega y producto de apoyo mostrado en imagen'],
            ['name' => 'Super Promo Verde', 'slug' => 'super-promo-verde-840', 'price' => 840, 'image' => 'super-promo-verde-840.jpg', 'includes' => 'BCAA Hardcore, Total War Reloaded y creatina'],
            ['name' => 'Promocion Whey Mutant y The Curse', 'slug' => 'promocion-whey-mutant-curse-1550', 'price' => 1550, 'image' => 'promocion-whey-mutant-curse-1550.jpg', 'includes' => 'Mutant Whey, Inlabs Whey Protein y The Curse'],
            ['name' => 'Promocion Whey, Creatina y Psychotic', 'slug' => 'promocion-whey-creatina-psychotic-1150', 'price' => 1150, 'image' => 'promocion-whey-creatina-psychotic-1150.jpg', 'includes' => 'Whey Inlabs, creatina Essential, Psychotic Black y amino mostrado en imagen'],
            ['name' => 'Paquete Amino Recovery', 'slug' => 'paquete-amino-recovery-990', 'price' => 990, 'image' => 'paquete-amino-recovery-990.jpg', 'includes' => 'Amino Energy, Amino Muscle Recovery y The Curse'],
            ['name' => 'Promocion Paquete Efectivo', 'slug' => 'promocion-paquete-efectivo-840', 'price' => 840, 'image' => 'promocion-paquete-efectivo-840.jpg', 'includes' => 'creatina monohidratada, essential pre workout y multivitaminico Inlabs'],
            ['name' => 'Promocion Gold Standard, Nitro y Psychotic', 'slug' => 'promocion-gold-standard-nitro-psychotic-2090', 'price' => 2090, 'image' => 'promocion-gold-standard-nitro-psychotic-2090.jpg', 'includes' => 'Cell Tech, Gold Standard Whey y Psychotic'],
            ['name' => 'Promocion Mass Infusion y Creatina', 'slug' => 'promocion-mass-infusion-creatina-940', 'price' => 940, 'image' => 'promocion-mass-infusion-creatina-940.jpg', 'includes' => 'Mass Infusion y creatina mostrada en imagen'],
            ['name' => 'Promocion Combat y Creatina', 'slug' => 'promocion-combat-creatina-890', 'price' => 890, 'image' => 'promocion-combat-creatina-890.jpg', 'includes' => 'Combat Protein Powder y creatina Inlabs'],
            ['name' => 'Promocion Creatina y Amino Energy', 'slug' => 'promocion-creatina-amino-energy-640', 'price' => 640, 'image' => 'promocion-creatina-amino-energy-640.jpg', 'includes' => 'creatina Essential y Amino Energy'],
            ['name' => 'Promocion Amino, Lipo y Nitraflex', 'slug' => 'promocion-amino-lipo-nitraflex-740', 'price' => 740, 'image' => 'promocion-amino-lipo-nitraflex-740.jpg', 'includes' => 'Amino Muscle Recovery, Lipo 6 y Nitraflex'],
            ['name' => 'Promocion C4, Creatina y Amino', 'slug' => 'promocion-c4-creatina-amino-1350', 'price' => 1350, 'image' => 'promocion-c4-creatina-amino-1350.jpg', 'includes' => 'C4 Whey Protein, creatina Inlabs y Amino Muscle Recovery'],
            ['name' => 'Promocion Creatina Birdman y Med Kit', 'slug' => 'promocion-creatina-birdman-med-kit-610', 'price' => 610, 'image' => 'promocion-creatina-birdman-med-kit-610.jpg', 'includes' => 'creatina Birdman y Med-Kit'],
            ['name' => 'Promocion Whey, Creatina y Veinz', 'slug' => 'promocion-whey-creatina-veinz-1090', 'price' => 1090, 'image' => 'promocion-whey-creatina-veinz-1090.jpg', 'includes' => 'Whey Protein, creatina Essential y Mr Veinz'],
            ['name' => 'Promocion Isopure, Creatina y Cbum', 'slug' => 'promocion-isopure-creatina-cbum-1630', 'price' => 1630, 'image' => 'promocion-isopure-creatina-cbum-1630.jpg', 'includes' => 'Isopure, creatina Inlabs y Cbum Essential'],
            ['name' => 'Promocion Clean Whey, Mass y Creatina', 'slug' => 'promocion-clean-whey-mass-creatina-980', 'price' => 980, 'image' => 'promocion-clean-whey-mass-creatina-980.jpg', 'includes' => 'Clean Whey Protein, Mass Extreme y creatina'],
            ['name' => 'Promocion Ryse y Breach', 'slug' => 'promocion-ryse-breach-810', 'price' => 810, 'image' => 'promocion-ryse-breach-810.jpg', 'includes' => 'Ryse y Breach'],
            ['name' => 'Promocion Multi Vitamin y Mutant', 'slug' => 'promocion-multivitamin-mutant-390', 'price' => 390, 'image' => 'promocion-multivitamin-mutant-390.jpg', 'includes' => 'Multi Vitamin Inlabs y Mutant'],
            ['name' => 'Promocion Gold Standard, Shaker y The Curse', 'slug' => 'promocion-gold-standard-shaker-curse-1580', 'price' => 1580, 'image' => 'promocion-gold-standard-shaker-curse-1580.jpg', 'includes' => 'Gold Standard Whey, shaker Insane Labz y The Curse'],
            ['name' => 'Promocion Carnivor, Creatina y The Curse', 'slug' => 'promocion-carnivor-creatina-curse-1150', 'price' => 1150, 'image' => 'promocion-carnivor-creatina-curse-1150.jpg', 'includes' => 'Carnivor, creatina y The Curse'],
            ['name' => 'Promocion Nitro Tech, Amino y Creatina', 'slug' => 'promocion-nitro-tech-amino-creatina-1620', 'price' => 1620, 'image' => 'promocion-nitro-tech-amino-creatina-1620.jpg', 'includes' => 'Nitro Tech, creatina y Amino Energy; regalo creatina Inlabs'],
            ['name' => 'Combo Especial Nitro Tech y Psychotic', 'slug' => 'combo-especial-nitro-tech-psychotic-1750', 'price' => 1750, 'image' => 'combo-especial-nitro-tech-psychotic-1750.jpg', 'includes' => 'Nitro Tech Whey Gold, creatina Birdman, Psychotic Gold y pre mostrado en imagen'],
            ['name' => 'Combo Especial Whey, Ryse y Creatina', 'slug' => 'combo-especial-whey-ryse-creatina-1730', 'price' => 1730, 'image' => 'combo-especial-whey-ryse-creatina-1730.jpg', 'includes' => 'Whey Inlabs, Ryse, creatina y productos mostrados en imagen'],
            ['name' => 'Promocion Carnivor, CLA y C4', 'slug' => 'promocion-carnivor-cla-c4-810', 'price' => 810, 'image' => 'promocion-carnivor-cla-c4-810.jpg', 'includes' => 'Carnivor Hardcore, CLA y C4 Original'],
            ['name' => 'Promocion Isolate, Creatina y Ghost', 'slug' => 'promocion-isolate-creatina-ghost-1410', 'price' => 1410, 'image' => 'promocion-isolate-creatina-ghost-1410.jpg', 'includes' => 'Isolate Inlabs, creatina Inlabs y Ghost Legend; regalo Amino Inlabs'],
            ['name' => 'Oferta Nitro Tech, Amino y Black Widow', 'slug' => 'oferta-nitro-tech-amino-black-1690', 'price' => 1690, 'image' => 'oferta-nitro-tech-amino-black-1690.jpg', 'includes' => 'Nitro Tech Whey Gold, Black Widow y Amino Energy'],
            ['name' => 'Combo Whey Inlabs y Nitro Tech', 'slug' => 'combo-whey-inlabs-nitro-tech-1370', 'price' => 1370, 'image' => 'combo-whey-inlabs-nitro-tech-1370.jpg', 'includes' => 'Whey Inlabs y Nitro Tech Ripped'],
            ['name' => 'Especial Amino Energy y Ghost', 'slug' => 'especial-amino-energy-ghost-1020', 'price' => 1020, 'image' => 'especial-amino-energy-ghost-1020.jpg', 'includes' => 'Amino Energy y Ghost Legend Pre Workout'],
            ['name' => 'Combo Completo Pro', 'slug' => 'combo-completo-pro-1960', 'price' => 1960, 'image' => 'combo-completo-pro-1960.jpg', 'includes' => 'Cbum Essential, creatina Dragon, Nitro Tech Whey Gold, BCAA Mutant Hardcore y multi mostrado en imagen'],
            ['name' => 'Combo Cell Tech, BCAA y Creatina', 'slug' => 'combo-cell-tech-bcaa-creatina-1190', 'price' => 1190, 'image' => 'combo-cell-tech-bcaa-creatina-1190.jpg', 'includes' => 'Cell-Tech Creatine, BCAA y creatina'],
            ['name' => 'Combo Full Performance', 'slug' => 'combo-full-performance-1050', 'price' => 1050, 'image' => 'combo-full-performance-1050.jpg', 'includes' => 'Psychotic Black, shaker y Isolate Inlabs'],
            ['name' => 'Combo Completo Pro Cbum, Amino y Beta', 'slug' => 'combo-completo-pro-cbum-amino-beta-730', 'price' => 730, 'image' => 'combo-completo-pro-cbum-amino-beta-730.jpg', 'includes' => 'Cbum Performance, Amino Inlabs y beta alanina'],
            ['name' => 'Pack Performance', 'slug' => 'pack-performance-1990', 'price' => 1990, 'image' => 'pack-performance-1990.jpg', 'includes' => 'NO-Xplode, Serious Mass y Whey Inlabs'],
            ['name' => 'Pack Entrenamiento Joker Nitro y Creatina', 'slug' => 'pack-entrenamiento-joker-nitro-creatina-1640', 'price' => 1640, 'image' => 'pack-entrenamiento-joker-nitro-creatina-1640.jpg', 'includes' => 'Joker, Nitro Tech y creatina Inlabs'],
            ['name' => 'Paquete Fitness EAA, Isolate y Creatina', 'slug' => 'paquete-fitness-eaa-isolate-creatina-1130', 'price' => 1130, 'image' => 'paquete-fitness-eaa-isolate-creatina-1130.jpg', 'includes' => 'EAA Recovery, Isolate Inlabs y creatina Inlabs'],
            ['name' => 'Paquete Pro Total', 'slug' => 'paquete-pro-total-1440', 'price' => 1440, 'image' => 'paquete-pro-total-1440.jpg', 'includes' => 'Mr Veinz, Cbum Essential, Omega Inlabs, Whey Inlabs y productos mostrados en imagen'],
            ['name' => 'Pack Activo', 'slug' => 'pack-activo-1070', 'price' => 1070, 'image' => 'pack-activo-1070.jpg', 'includes' => 'BCAA Inlabs, Mutant Whey y Stimul8'],
            ['name' => 'Promocion Pro', 'slug' => 'promocion-pro-900', 'price' => 900, 'image' => 'promocion-pro-900.jpg', 'includes' => 'Clean Whey Protein y Psychopath'],
            ['name' => 'Paquete Fitness Mutant, Psychotic y Creatina', 'slug' => 'paquete-fitness-mutant-psychotic-creatina-1270', 'price' => 1270, 'image' => 'paquete-fitness-mutant-psychotic-creatina-1270.jpg', 'includes' => 'Mutant Whey, Psychotic y creatina Inlabs'],
            ['name' => 'Paquete Fitness Gold Standard', 'slug' => 'paquete-fitness-gold-standard-1250', 'price' => 1250, 'image' => 'paquete-fitness-gold-standard-1250.jpg', 'includes' => 'Whey Inlabs, Gold Standard y creatina Inlabs'],
            ['name' => 'Pack Avanzado Amino y Gold Standard', 'slug' => 'pack-avanzado-amino-gold-standard-1270', 'price' => 1270, 'image' => 'pack-avanzado-amino-gold-standard-1270.jpg', 'includes' => 'Amino Energy, Gold Standard Whey, shaker y Psychotic'],
            ['name' => 'Paquete Completo Veinz y Psychotic', 'slug' => 'paquete-completo-veinz-psychotic-1130', 'price' => 1130, 'image' => 'paquete-completo-veinz-psychotic-1130.jpg', 'includes' => 'Mr Veinz y Psychotic'],
            ['name' => 'Paquete Completo Amino y Whey', 'slug' => 'paquete-completo-amino-whey-1340', 'price' => 1340, 'image' => 'paquete-completo-amino-whey-1340.jpg', 'includes' => 'Amino Inlabs, Omega Inlabs, Whey Inlabs y Gold Standard'],
            ['name' => 'Pack Avanzado BCAA y Whey', 'slug' => 'pack-avanzado-bcaa-whey-930', 'price' => 930, 'image' => 'pack-avanzado-bcaa-whey-930.jpg', 'includes' => 'BCAA Inlabs, Whey Inlabs y shaker'],
            ['name' => 'Pack Avanzado Mutant Mass', 'slug' => 'pack-avanzado-mutant-mass-1490', 'price' => 1490, 'image' => 'pack-avanzado-mutant-mass-1490.jpg', 'includes' => 'shaker, glutamina, Mutant Mass, The Curse y creatina Inlabs'],
            ['name' => 'Pack Disponible', 'slug' => 'pack-disponible-2190', 'price' => 2190, 'image' => 'pack-disponible-2190.jpg', 'includes' => 'Whey Inlabs, productos de soporte y creatina mostrados en imagen'],
            ['name' => 'Pack Completo Gold Standard y Cbum', 'slug' => 'pack-completo-gold-standard-cbum-1400', 'price' => 1400, 'image' => 'pack-completo-gold-standard-cbum-1400.jpg', 'includes' => 'creatina, Gold Standard Whey, Cbum Essential y pre workout'],
            ['name' => 'Black Pack', 'slug' => 'black-pack-1820', 'price' => 1820, 'image' => 'black-pack-1820.jpg', 'includes' => 'Isolate, creatina, glutamina, Psychotic, BCAAS y regalo mostrado en imagen'],
            ['name' => 'Fitness Pack', 'slug' => 'fitness-pack-2950', 'price' => 2950, 'image' => 'fitness-pack-2950.jpg', 'includes' => 'Nitro Tech, creatina, amino, Nitraflex, glutamina y productos mostrados en imagen'],
            ['name' => 'Paquete Pro', 'slug' => 'paquete-pro-2130', 'price' => 2130, 'image' => 'paquete-pro-2130.jpg', 'includes' => 'Whey Inlabs, creatina, shaker, glutamina y Breach'],
            ['name' => 'Power Pack', 'slug' => 'power-pack-2380', 'price' => 2380, 'image' => 'power-pack-2380.jpg', 'includes' => 'Isolate, creatina, amino, pre workout, Stimul8 y productos mostrados en imagen'],
            ['name' => 'Iron Pack', 'slug' => 'iron-pack-2790', 'price' => 2790, 'image' => 'iron-pack-2790.jpg', 'includes' => 'combo completo de suplementos mostrados en imagen, con regalo incluido'],
            ['name' => 'New Promo', 'slug' => 'new-promo-1750', 'price' => 1750, 'image' => 'new-promo-1750.jpg', 'includes' => 'Isolate, creatina, amino, BCAAS y productos mostrados en imagen'],
            ['name' => 'Powerful Blow Pack', 'slug' => 'powerful-blow-pack-1910', 'price' => 1910, 'image' => 'powerful-blow-pack-1910.jpg', 'includes' => 'pre workout, Total War, Nitraflex, Psychotic y Mr Veinz'],
            ['name' => 'Promo Vigente', 'slug' => 'promo-vigente-1730', 'price' => 1730, 'image' => 'promo-vigente-1730.jpg', 'includes' => 'Combat, shaker, C4, Total War y Stimul8'],
            ['name' => 'Nitro Pack', 'slug' => 'nitro-pack-1620', 'price' => 1620, 'image' => 'nitro-pack-1620.jpg', 'includes' => 'Nitro Tech, creatina y productos mostrados en imagen; regalo incluido'],
            ['name' => 'Go Prime Pack', 'slug' => 'go-prime-pack-1550', 'price' => 1550, 'image' => 'go-prime-pack-1550.jpg', 'includes' => 'glutamina, arginina, creatina, citrulina y productos mostrados en imagen; regalo incluido'],
            ['name' => 'Powder Pack', 'slug' => 'powder-pack-2390', 'price' => 2390, 'image' => 'powder-pack-2390.jpg', 'includes' => 'whey, creatina, multivitaminico, glutamina, shaker y productos mostrados en imagen'],
            ['name' => 'Energy Pack', 'slug' => 'energy-pack-1930', 'price' => 1930, 'image' => 'energy-pack-1930.jpg', 'includes' => 'whey, creatina, amino, Psychotic y productos mostrados en imagen'],
            ['name' => 'The Complete Pack', 'slug' => 'the-complete-pack-1950', 'price' => 1950, 'image' => 'the-complete-pack-1950.jpg', 'includes' => 'Isolate, shaker, Total War, Mutant, C4 y productos mostrados en imagen; regalo incluido'],
            ['name' => 'Recovery & Power Pack', 'slug' => 'recovery-power-pack-1730', 'price' => 1730, 'image' => 'recovery-power-pack-1730.jpg', 'includes' => 'Whey Inlabs, creatina, glutamina, amino, BCAAS y productos mostrados en imagen; regalo incluido'],
            ['name' => 'New Pack', 'slug' => 'new-pack-2550', 'price' => 2550, 'image' => 'new-pack-2550.jpg', 'includes' => 'whey, glutamina, amino, pre workout y productos mostrados en imagen; regalo incluido'],
            ['name' => 'Total Pack', 'slug' => 'total-pack-2010', 'price' => 2010, 'image' => 'total-pack-2010.jpg', 'includes' => 'combo activo de suplementos mostrados en imagen, con regalo incluido'],
            ['name' => 'Gainz Box Pack', 'slug' => 'gainz-box-pack-2250', 'price' => 2250, 'image' => 'gainz-box-pack-2250.jpg', 'includes' => 'Isopure, carnitina, lipo, omega, C4 y productos mostrados en imagen; regalo incluido'],
            ['name' => 'Mass Max Pack', 'slug' => 'mass-max-pack-1830', 'price' => 1830, 'image' => 'mass-max-pack-1830.jpg', 'includes' => 'Mass Infusion, Whey Inlabs, creatina y productos mostrados en imagen; regalo incluido'],
            ['name' => 'Nuevo Combo', 'slug' => 'nuevo-combo-1710', 'price' => 1710, 'image' => 'nuevo-combo-1710.jpg', 'includes' => 'creatina, shaker, glutamina, pre workout y productos mostrados en imagen; regalo incluido'],
            ['name' => 'Maximum Pack', 'slug' => 'maximum-pack-1630', 'price' => 1630, 'image' => 'maximum-pack-1630.jpg', 'includes' => 'creatina, cafeina, Psychotic y productos mostrados en imagen; regalo incluido'],
            ['name' => 'Combo Wellness', 'slug' => 'combo-wellness-2630', 'price' => 2630, 'image' => 'combo-wellness-2630.jpg', 'includes' => 'Isolate, creatina, omega, shaker y productos mostrados en imagen; regalo incluido'],
            ['name' => 'Kit Performance', 'slug' => 'kit-performance-1860', 'price' => 1860, 'image' => 'kit-performance-1860.jpg', 'includes' => 'creatina, BCAAS, Psychotic, proteina y productos mostrados en imagen; regalo incluido'],
            ['name' => 'Strong Pack', 'slug' => 'strong-pack-2490', 'price' => 2490, 'image' => 'strong-pack-2490.jpg', 'includes' => 'Whey Protein, creatina, omega y productos mostrados en imagen; regalo incluido'],
            ['name' => 'Magnum Pack', 'slug' => 'magnum-pack-2990', 'price' => 2990, 'image' => 'magnum-pack-2990.jpg', 'includes' => 'whey, Nitro Tech, pre workout, creatina y productos mostrados en imagen'],
            ['name' => 'Super Pack', 'slug' => 'super-pack-2720', 'price' => 2720, 'image' => 'super-pack-2720.jpg', 'includes' => 'Nitro Tech, Isopure, creatina y productos mostrados en imagen; regalo incluido'],
            ['name' => 'Standard Pack', 'slug' => 'standard-pack-2460', 'price' => 2460, 'image' => 'standard-pack-2460.jpg', 'includes' => 'whey, creatina y productos mostrados en imagen; regalo incluido'],
            ['name' => 'Nuevo Combo Isolate', 'slug' => 'nuevo-combo-isolate-2370', 'price' => 2370, 'image' => 'nuevo-combo-isolate-2370.jpg', 'includes' => 'ISO100, Isolate, creatina y productos mostrados en imagen; regalo incluido'],
            ['name' => 'Fitness Pro Pack', 'slug' => 'fitness-pro-pack-1350', 'price' => 1350, 'image' => 'fitness-pro-pack-1350.jpg', 'includes' => 'ISO Lean, creatina, Amino Energy y productos mostrados en imagen; regalo incluido'],
            ['name' => 'Paquete Performance', 'slug' => 'paquete-performance-1770', 'price' => 1770, 'image' => 'paquete-performance-1770.jpg', 'includes' => 'creatina GAT, Gold Standard Whey y Best BCAA'],
            ['name' => 'Paquete Evolucion', 'slug' => 'paquete-evolucion-2190', 'price' => 2190, 'image' => 'paquete-evolucion-2190.jpg', 'includes' => 'ISO100, amino, pre workout y productos mostrados en imagen'],
        ];
    }
}
