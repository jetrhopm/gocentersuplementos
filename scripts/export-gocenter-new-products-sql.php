<?php

use Database\Seeders\GoCenterNewProductsSeeder;

require __DIR__.'/../vendor/autoload.php';

$seeder = new GoCenterNewProductsSeeder();
$reflection = new ReflectionClass($seeder);
$productsMethod = $reflection->getMethod('products');
$productsMethod->setAccessible(true);
$descriptionMethod = $reflection->getMethod('description');
$descriptionMethod->setAccessible(true);

$products = $productsMethod->invoke($seeder);
$output = __DIR__.'/../database/exports/gocenter_new_products_only.sql';

function sql_quote(?string $value): string
{
    if ($value === null) {
        return 'NULL';
    }

    return "'".str_replace(["\\", "'"], ["\\\\", "\\'"], $value)."'";
}

$lines = [
    '-- Go Center Suplementos - productos nuevos categoria Packs Go Center',
    '-- Importar despues de limpiar la categoria desde el panel admin.',
    '-- Este archivo NO borra productos existentes.',
    'START TRANSACTION;',
    '',
    "INSERT INTO categories (`name`, `slug`, `description`, `active`, `sort_order`, `created_at`, `updated_at`)",
    "SELECT 'Packs Go Center', 'packs-gocenter', 'Combos y promociones Go Center Suplementos.', 1, 10, NOW(), NOW()",
    "WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'packs-gocenter');",
    '',
    "SET @category_id := (SELECT id FROM categories WHERE slug = 'packs-gocenter' LIMIT 1);",
    '',
];

foreach ($products as $item) {
    $name = $item['name'];
    $slug = $item['slug'];
    $sku = 'GC-'.strtoupper(str_replace('-', '-', $slug));
    $description = $descriptionMethod->invoke($seeder, $item);
    $imagePath = 'assets/gocenter/products/'.$item['image'];
    $metaTitle = $name.' | Go Center Suplementos';

    $lines[] = '-- '.$name;
    $lines[] = 'INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES';
    $lines[] = '('.
        '@category_id, '.
        sql_quote($name).', '.
        sql_quote($slug).', '.
        sql_quote($sku).', '.
        sql_quote('Go Center').', '.
        sql_quote($description).', '.
        number_format((float) $item['price'], 2, '.', '').', '.
        'NULL, '.
        '10, '.
        '1, '.
        '1, '.
        sql_quote($metaTitle).', '.
        sql_quote($description).', '.
        'NOW(), NOW()'.
        ')';
    $lines[] = 'ON DUPLICATE KEY UPDATE';
    $lines[] = '  `category_id` = VALUES(`category_id`),';
    $lines[] = '  `name` = VALUES(`name`),';
    $lines[] = '  `sku` = VALUES(`sku`),';
    $lines[] = '  `brand` = VALUES(`brand`),';
    $lines[] = '  `description` = VALUES(`description`),';
    $lines[] = '  `price` = VALUES(`price`),';
    $lines[] = '  `compare_at_price` = VALUES(`compare_at_price`),';
    $lines[] = '  `stock` = VALUES(`stock`),';
    $lines[] = '  `featured` = VALUES(`featured`),';
    $lines[] = '  `active` = VALUES(`active`),';
    $lines[] = '  `meta_title` = VALUES(`meta_title`),';
    $lines[] = '  `meta_description` = VALUES(`meta_description`),';
    $lines[] = '  `updated_at` = NOW();';
    $lines[] = '';
    $lines[] = 'SET @product_id := (SELECT id FROM products WHERE slug = '.sql_quote($slug).' LIMIT 1);';
    $lines[] = 'DELETE FROM product_images WHERE product_id = @product_id;';
    $lines[] = 'INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES';
    $lines[] = '(@product_id, '.sql_quote($imagePath).', '.sql_quote($name).', 0, NOW(), NOW());';
    $lines[] = '';
}

$lines[] = 'COMMIT;';
$lines[] = '';

file_put_contents($output, implode(PHP_EOL, $lines));

echo $output.PHP_EOL;
echo count($products).' productos exportados.'.PHP_EOL;
