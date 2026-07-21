-- Go Center Suplementos - productos nuevos categoria Packs Go Center
-- Importar despues de limpiar la categoria desde el panel admin.
-- Este archivo NO borra productos existentes.
START TRANSACTION;

INSERT INTO categories (`name`, `slug`, `description`, `active`, `sort_order`, `created_at`, `updated_at`)
SELECT 'Packs Go Center', 'packs-gocenter', 'Combos y promociones Go Center Suplementos.', 1, 10, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'packs-gocenter');

SET @category_id := (SELECT id FROM categories WHERE slug = 'packs-gocenter' LIMIT 1);

-- Promocion Nitro Tech, Veinz y Amino Energy
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Nitro Tech, Veinz y Amino Energy', 'promocion-nitro-tech-veinz-amino-energy-1970', 'GC-PROMOCION-NITRO-TECH-VEINZ-AMINO-ENERGY-1970', 'Go Center', 'Pack promocional Go Center. Incluye: Nitro Tech, Mr Veinz y Amino Energy. Costo de envio no incluido.', 1970.00, NULL, 10, 1, 1, 'Promocion Nitro Tech, Veinz y Amino Energy | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Nitro Tech, Mr Veinz y Amino Energy. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-nitro-tech-veinz-amino-energy-1970' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-nitro-tech-veinz-amino-energy-1970.jpg', 'Promocion Nitro Tech, Veinz y Amino Energy', 0, NOW(), NOW());

-- Combo Favorito Mutant Mass
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Combo Favorito Mutant Mass', 'combo-favorito-mutant-mass-1280', 'GC-COMBO-FAVORITO-MUTANT-MASS-1280', 'Go Center', 'Pack promocional Go Center. Incluye: creatina GAT, Mutant Mass Extreme y Psychotic Black; regalo BCAAS. Costo de envio no incluido.', 1280.00, NULL, 10, 1, 1, 'Combo Favorito Mutant Mass | Go Center Suplementos', 'Pack promocional Go Center. Incluye: creatina GAT, Mutant Mass Extreme y Psychotic Black; regalo BCAAS. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'combo-favorito-mutant-mass-1280' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/combo-favorito-mutant-mass-1280.jpg', 'Combo Favorito Mutant Mass', 0, NOW(), NOW());

-- Combo Completo Creatina, Whey y Amino
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Combo Completo Creatina, Whey y Amino', 'combo-completo-creatina-whey-amino-1390', 'GC-COMBO-COMPLETO-CREATINA-WHEY-AMINO-1390', 'Go Center', 'Pack promocional Go Center. Incluye: creatina, whey protein, cafeina, Total War y Amino. Costo de envio no incluido.', 1390.00, NULL, 10, 1, 1, 'Combo Completo Creatina, Whey y Amino | Go Center Suplementos', 'Pack promocional Go Center. Incluye: creatina, whey protein, cafeina, Total War y Amino. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'combo-completo-creatina-whey-amino-1390' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/combo-completo-creatina-whey-amino-1390.jpg', 'Combo Completo Creatina, Whey y Amino', 0, NOW(), NOW());

-- Pack Entrenamiento Completo
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Pack Entrenamiento Completo', 'pack-entrenamiento-completo-1090', 'GC-PACK-ENTRENAMIENTO-COMPLETO-1090', 'Go Center', 'Pack promocional Go Center. Incluye: pre workout, Venom Essential y creatina BPI. Costo de envio no incluido.', 1090.00, NULL, 10, 1, 1, 'Pack Entrenamiento Completo | Go Center Suplementos', 'Pack promocional Go Center. Incluye: pre workout, Venom Essential y creatina BPI. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'pack-entrenamiento-completo-1090' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/pack-entrenamiento-completo-1090.jpg', 'Pack Entrenamiento Completo', 0, NOW(), NOW());

-- Super Promo Azul
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Super Promo Azul', 'super-promo-azul-1860', 'GC-SUPER-PROMO-AZUL-1860', 'Go Center', 'Pack promocional Go Center. Incluye: creatina GAT, omega, Isolate Inlabs y Pre Ghost. Costo de envio no incluido.', 1860.00, NULL, 10, 1, 1, 'Super Promo Azul | Go Center Suplementos', 'Pack promocional Go Center. Incluye: creatina GAT, omega, Isolate Inlabs y Pre Ghost. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'super-promo-azul-1860' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/super-promo-azul-1860.jpg', 'Super Promo Azul', 0, NOW(), NOW());

-- Pack Entrenamiento Completo Morado
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Pack Entrenamiento Completo Morado', 'pack-entrenamiento-completo-morado-1290', 'GC-PACK-ENTRENAMIENTO-COMPLETO-MORADO-1290', 'Go Center', 'Pack promocional Go Center. Incluye: Whey Inlabs, Venom Essential, Breach y creatina Inlabs. Costo de envio no incluido.', 1290.00, NULL, 10, 1, 1, 'Pack Entrenamiento Completo Morado | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Whey Inlabs, Venom Essential, Breach y creatina Inlabs. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'pack-entrenamiento-completo-morado-1290' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/pack-entrenamiento-completo-morado-1290.jpg', 'Pack Entrenamiento Completo Morado', 0, NOW(), NOW());

-- Promo Completa BCAAS, Pre e Isopure
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promo Completa BCAAS, Pre e Isopure', 'promo-completa-bcaas-pre-isopure-1090', 'GC-PROMO-COMPLETA-BCAAS-PRE-ISOPURE-1090', 'Go Center', 'Pack promocional Go Center. Incluye: BCAAS, essential pre workout, Isopure, omega y producto de apoyo mostrado en imagen. Costo de envio no incluido.', 1090.00, NULL, 10, 1, 1, 'Promo Completa BCAAS, Pre e Isopure | Go Center Suplementos', 'Pack promocional Go Center. Incluye: BCAAS, essential pre workout, Isopure, omega y producto de apoyo mostrado en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promo-completa-bcaas-pre-isopure-1090' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promo-completa-bcaas-pre-isopure-1090.jpg', 'Promo Completa BCAAS, Pre e Isopure', 0, NOW(), NOW());

-- Super Promo Verde
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Super Promo Verde', 'super-promo-verde-840', 'GC-SUPER-PROMO-VERDE-840', 'Go Center', 'Pack promocional Go Center. Incluye: BCAA Hardcore, Total War Reloaded y creatina. Costo de envio no incluido.', 840.00, NULL, 10, 1, 1, 'Super Promo Verde | Go Center Suplementos', 'Pack promocional Go Center. Incluye: BCAA Hardcore, Total War Reloaded y creatina. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'super-promo-verde-840' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/super-promo-verde-840.jpg', 'Super Promo Verde', 0, NOW(), NOW());

-- Promocion Whey Mutant y The Curse
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Whey Mutant y The Curse', 'promocion-whey-mutant-curse-1550', 'GC-PROMOCION-WHEY-MUTANT-CURSE-1550', 'Go Center', 'Pack promocional Go Center. Incluye: Mutant Whey, Inlabs Whey Protein y The Curse. Costo de envio no incluido.', 1550.00, NULL, 10, 1, 1, 'Promocion Whey Mutant y The Curse | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Mutant Whey, Inlabs Whey Protein y The Curse. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-whey-mutant-curse-1550' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-whey-mutant-curse-1550.jpg', 'Promocion Whey Mutant y The Curse', 0, NOW(), NOW());

-- Promocion Whey, Creatina y Psychotic
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Whey, Creatina y Psychotic', 'promocion-whey-creatina-psychotic-1150', 'GC-PROMOCION-WHEY-CREATINA-PSYCHOTIC-1150', 'Go Center', 'Pack promocional Go Center. Incluye: Whey Inlabs, creatina Essential, Psychotic Black y amino mostrado en imagen. Costo de envio no incluido.', 1150.00, NULL, 10, 1, 1, 'Promocion Whey, Creatina y Psychotic | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Whey Inlabs, creatina Essential, Psychotic Black y amino mostrado en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-whey-creatina-psychotic-1150' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-whey-creatina-psychotic-1150.jpg', 'Promocion Whey, Creatina y Psychotic', 0, NOW(), NOW());

-- Paquete Amino Recovery
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Paquete Amino Recovery', 'paquete-amino-recovery-990', 'GC-PAQUETE-AMINO-RECOVERY-990', 'Go Center', 'Pack promocional Go Center. Incluye: Amino Energy, Amino Muscle Recovery y The Curse. Costo de envio no incluido.', 990.00, NULL, 10, 1, 1, 'Paquete Amino Recovery | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Amino Energy, Amino Muscle Recovery y The Curse. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'paquete-amino-recovery-990' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/paquete-amino-recovery-990.jpg', 'Paquete Amino Recovery', 0, NOW(), NOW());

-- Promocion Paquete Efectivo
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Paquete Efectivo', 'promocion-paquete-efectivo-840', 'GC-PROMOCION-PAQUETE-EFECTIVO-840', 'Go Center', 'Pack promocional Go Center. Incluye: creatina monohidratada, essential pre workout y multivitaminico Inlabs. Costo de envio no incluido.', 840.00, NULL, 10, 1, 1, 'Promocion Paquete Efectivo | Go Center Suplementos', 'Pack promocional Go Center. Incluye: creatina monohidratada, essential pre workout y multivitaminico Inlabs. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-paquete-efectivo-840' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-paquete-efectivo-840.jpg', 'Promocion Paquete Efectivo', 0, NOW(), NOW());

-- Promocion Gold Standard, Nitro y Psychotic
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Gold Standard, Nitro y Psychotic', 'promocion-gold-standard-nitro-psychotic-2090', 'GC-PROMOCION-GOLD-STANDARD-NITRO-PSYCHOTIC-2090', 'Go Center', 'Pack promocional Go Center. Incluye: Cell Tech, Gold Standard Whey y Psychotic. Costo de envio no incluido.', 2090.00, NULL, 10, 1, 1, 'Promocion Gold Standard, Nitro y Psychotic | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Cell Tech, Gold Standard Whey y Psychotic. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-gold-standard-nitro-psychotic-2090' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-gold-standard-nitro-psychotic-2090.jpg', 'Promocion Gold Standard, Nitro y Psychotic', 0, NOW(), NOW());

-- Promocion Mass Infusion y Creatina
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Mass Infusion y Creatina', 'promocion-mass-infusion-creatina-940', 'GC-PROMOCION-MASS-INFUSION-CREATINA-940', 'Go Center', 'Pack promocional Go Center. Incluye: Mass Infusion y creatina mostrada en imagen. Costo de envio no incluido.', 940.00, NULL, 10, 1, 1, 'Promocion Mass Infusion y Creatina | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Mass Infusion y creatina mostrada en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-mass-infusion-creatina-940' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-mass-infusion-creatina-940.jpg', 'Promocion Mass Infusion y Creatina', 0, NOW(), NOW());

-- Promocion Combat y Creatina
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Combat y Creatina', 'promocion-combat-creatina-890', 'GC-PROMOCION-COMBAT-CREATINA-890', 'Go Center', 'Pack promocional Go Center. Incluye: Combat Protein Powder y creatina Inlabs. Costo de envio no incluido.', 890.00, NULL, 10, 1, 1, 'Promocion Combat y Creatina | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Combat Protein Powder y creatina Inlabs. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-combat-creatina-890' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-combat-creatina-890.jpg', 'Promocion Combat y Creatina', 0, NOW(), NOW());

-- Promocion Creatina y Amino Energy
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Creatina y Amino Energy', 'promocion-creatina-amino-energy-640', 'GC-PROMOCION-CREATINA-AMINO-ENERGY-640', 'Go Center', 'Pack promocional Go Center. Incluye: creatina Essential y Amino Energy. Costo de envio no incluido.', 640.00, NULL, 10, 1, 1, 'Promocion Creatina y Amino Energy | Go Center Suplementos', 'Pack promocional Go Center. Incluye: creatina Essential y Amino Energy. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-creatina-amino-energy-640' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-creatina-amino-energy-640.jpg', 'Promocion Creatina y Amino Energy', 0, NOW(), NOW());

-- Promocion Amino, Lipo y Nitraflex
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Amino, Lipo y Nitraflex', 'promocion-amino-lipo-nitraflex-740', 'GC-PROMOCION-AMINO-LIPO-NITRAFLEX-740', 'Go Center', 'Pack promocional Go Center. Incluye: Amino Muscle Recovery, Lipo 6 y Nitraflex. Costo de envio no incluido.', 740.00, NULL, 10, 1, 1, 'Promocion Amino, Lipo y Nitraflex | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Amino Muscle Recovery, Lipo 6 y Nitraflex. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-amino-lipo-nitraflex-740' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-amino-lipo-nitraflex-740.jpg', 'Promocion Amino, Lipo y Nitraflex', 0, NOW(), NOW());

-- Promocion C4, Creatina y Amino
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion C4, Creatina y Amino', 'promocion-c4-creatina-amino-1350', 'GC-PROMOCION-C4-CREATINA-AMINO-1350', 'Go Center', 'Pack promocional Go Center. Incluye: C4 Whey Protein, creatina Inlabs y Amino Muscle Recovery. Costo de envio no incluido.', 1350.00, NULL, 10, 1, 1, 'Promocion C4, Creatina y Amino | Go Center Suplementos', 'Pack promocional Go Center. Incluye: C4 Whey Protein, creatina Inlabs y Amino Muscle Recovery. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-c4-creatina-amino-1350' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-c4-creatina-amino-1350.jpg', 'Promocion C4, Creatina y Amino', 0, NOW(), NOW());

-- Promocion Creatina Birdman y Med Kit
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Creatina Birdman y Med Kit', 'promocion-creatina-birdman-med-kit-610', 'GC-PROMOCION-CREATINA-BIRDMAN-MED-KIT-610', 'Go Center', 'Pack promocional Go Center. Incluye: creatina Birdman y Med-Kit. Costo de envio no incluido.', 610.00, NULL, 10, 1, 1, 'Promocion Creatina Birdman y Med Kit | Go Center Suplementos', 'Pack promocional Go Center. Incluye: creatina Birdman y Med-Kit. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-creatina-birdman-med-kit-610' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-creatina-birdman-med-kit-610.jpg', 'Promocion Creatina Birdman y Med Kit', 0, NOW(), NOW());

-- Promocion Whey, Creatina y Veinz
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Whey, Creatina y Veinz', 'promocion-whey-creatina-veinz-1090', 'GC-PROMOCION-WHEY-CREATINA-VEINZ-1090', 'Go Center', 'Pack promocional Go Center. Incluye: Whey Protein, creatina Essential y Mr Veinz. Costo de envio no incluido.', 1090.00, NULL, 10, 1, 1, 'Promocion Whey, Creatina y Veinz | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Whey Protein, creatina Essential y Mr Veinz. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-whey-creatina-veinz-1090' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-whey-creatina-veinz-1090.jpg', 'Promocion Whey, Creatina y Veinz', 0, NOW(), NOW());

-- Promocion Isopure, Creatina y Cbum
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Isopure, Creatina y Cbum', 'promocion-isopure-creatina-cbum-1630', 'GC-PROMOCION-ISOPURE-CREATINA-CBUM-1630', 'Go Center', 'Pack promocional Go Center. Incluye: Isopure, creatina Inlabs y Cbum Essential. Costo de envio no incluido.', 1630.00, NULL, 10, 1, 1, 'Promocion Isopure, Creatina y Cbum | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Isopure, creatina Inlabs y Cbum Essential. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-isopure-creatina-cbum-1630' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-isopure-creatina-cbum-1630.jpg', 'Promocion Isopure, Creatina y Cbum', 0, NOW(), NOW());

-- Promocion Clean Whey, Mass y Creatina
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Clean Whey, Mass y Creatina', 'promocion-clean-whey-mass-creatina-980', 'GC-PROMOCION-CLEAN-WHEY-MASS-CREATINA-980', 'Go Center', 'Pack promocional Go Center. Incluye: Clean Whey Protein, Mass Extreme y creatina. Costo de envio no incluido.', 980.00, NULL, 10, 1, 1, 'Promocion Clean Whey, Mass y Creatina | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Clean Whey Protein, Mass Extreme y creatina. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-clean-whey-mass-creatina-980' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-clean-whey-mass-creatina-980.jpg', 'Promocion Clean Whey, Mass y Creatina', 0, NOW(), NOW());

-- Promocion Ryse y Breach
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Ryse y Breach', 'promocion-ryse-breach-810', 'GC-PROMOCION-RYSE-BREACH-810', 'Go Center', 'Pack promocional Go Center. Incluye: Ryse y Breach. Costo de envio no incluido.', 810.00, NULL, 10, 1, 1, 'Promocion Ryse y Breach | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Ryse y Breach. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-ryse-breach-810' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-ryse-breach-810.jpg', 'Promocion Ryse y Breach', 0, NOW(), NOW());

-- Promocion Multi Vitamin y Mutant
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Multi Vitamin y Mutant', 'promocion-multivitamin-mutant-390', 'GC-PROMOCION-MULTIVITAMIN-MUTANT-390', 'Go Center', 'Pack promocional Go Center. Incluye: Multi Vitamin Inlabs y Mutant. Costo de envio no incluido.', 390.00, NULL, 10, 1, 1, 'Promocion Multi Vitamin y Mutant | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Multi Vitamin Inlabs y Mutant. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-multivitamin-mutant-390' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-multivitamin-mutant-390.jpg', 'Promocion Multi Vitamin y Mutant', 0, NOW(), NOW());

-- Promocion Gold Standard, Shaker y The Curse
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Gold Standard, Shaker y The Curse', 'promocion-gold-standard-shaker-curse-1580', 'GC-PROMOCION-GOLD-STANDARD-SHAKER-CURSE-1580', 'Go Center', 'Pack promocional Go Center. Incluye: Gold Standard Whey, shaker Insane Labz y The Curse. Costo de envio no incluido.', 1580.00, NULL, 10, 1, 1, 'Promocion Gold Standard, Shaker y The Curse | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Gold Standard Whey, shaker Insane Labz y The Curse. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-gold-standard-shaker-curse-1580' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-gold-standard-shaker-curse-1580.jpg', 'Promocion Gold Standard, Shaker y The Curse', 0, NOW(), NOW());

-- Promocion Carnivor, Creatina y The Curse
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Carnivor, Creatina y The Curse', 'promocion-carnivor-creatina-curse-1150', 'GC-PROMOCION-CARNIVOR-CREATINA-CURSE-1150', 'Go Center', 'Pack promocional Go Center. Incluye: Carnivor, creatina y The Curse. Costo de envio no incluido.', 1150.00, NULL, 10, 1, 1, 'Promocion Carnivor, Creatina y The Curse | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Carnivor, creatina y The Curse. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-carnivor-creatina-curse-1150' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-carnivor-creatina-curse-1150.jpg', 'Promocion Carnivor, Creatina y The Curse', 0, NOW(), NOW());

-- Promocion Nitro Tech, Amino y Creatina
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Nitro Tech, Amino y Creatina', 'promocion-nitro-tech-amino-creatina-1620', 'GC-PROMOCION-NITRO-TECH-AMINO-CREATINA-1620', 'Go Center', 'Pack promocional Go Center. Incluye: Nitro Tech, creatina y Amino Energy; regalo creatina Inlabs. Costo de envio no incluido.', 1620.00, NULL, 10, 1, 1, 'Promocion Nitro Tech, Amino y Creatina | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Nitro Tech, creatina y Amino Energy; regalo creatina Inlabs. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-nitro-tech-amino-creatina-1620' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-nitro-tech-amino-creatina-1620.jpg', 'Promocion Nitro Tech, Amino y Creatina', 0, NOW(), NOW());

-- Combo Especial Nitro Tech y Psychotic
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Combo Especial Nitro Tech y Psychotic', 'combo-especial-nitro-tech-psychotic-1750', 'GC-COMBO-ESPECIAL-NITRO-TECH-PSYCHOTIC-1750', 'Go Center', 'Pack promocional Go Center. Incluye: Nitro Tech Whey Gold, creatina Birdman, Psychotic Gold y pre mostrado en imagen. Costo de envio no incluido.', 1750.00, NULL, 10, 1, 1, 'Combo Especial Nitro Tech y Psychotic | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Nitro Tech Whey Gold, creatina Birdman, Psychotic Gold y pre mostrado en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'combo-especial-nitro-tech-psychotic-1750' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/combo-especial-nitro-tech-psychotic-1750.jpg', 'Combo Especial Nitro Tech y Psychotic', 0, NOW(), NOW());

-- Combo Especial Whey, Ryse y Creatina
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Combo Especial Whey, Ryse y Creatina', 'combo-especial-whey-ryse-creatina-1730', 'GC-COMBO-ESPECIAL-WHEY-RYSE-CREATINA-1730', 'Go Center', 'Pack promocional Go Center. Incluye: Whey Inlabs, Ryse, creatina y productos mostrados en imagen. Costo de envio no incluido.', 1730.00, NULL, 10, 1, 1, 'Combo Especial Whey, Ryse y Creatina | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Whey Inlabs, Ryse, creatina y productos mostrados en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'combo-especial-whey-ryse-creatina-1730' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/combo-especial-whey-ryse-creatina-1730.jpg', 'Combo Especial Whey, Ryse y Creatina', 0, NOW(), NOW());

-- Promocion Carnivor, CLA y C4
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Carnivor, CLA y C4', 'promocion-carnivor-cla-c4-810', 'GC-PROMOCION-CARNIVOR-CLA-C4-810', 'Go Center', 'Pack promocional Go Center. Incluye: Carnivor Hardcore, CLA y C4 Original. Costo de envio no incluido.', 810.00, NULL, 10, 1, 1, 'Promocion Carnivor, CLA y C4 | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Carnivor Hardcore, CLA y C4 Original. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-carnivor-cla-c4-810' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-carnivor-cla-c4-810.jpg', 'Promocion Carnivor, CLA y C4', 0, NOW(), NOW());

-- Promocion Isolate, Creatina y Ghost
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Isolate, Creatina y Ghost', 'promocion-isolate-creatina-ghost-1410', 'GC-PROMOCION-ISOLATE-CREATINA-GHOST-1410', 'Go Center', 'Pack promocional Go Center. Incluye: Isolate Inlabs, creatina Inlabs y Ghost Legend; regalo Amino Inlabs. Costo de envio no incluido.', 1410.00, NULL, 10, 1, 1, 'Promocion Isolate, Creatina y Ghost | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Isolate Inlabs, creatina Inlabs y Ghost Legend; regalo Amino Inlabs. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-isolate-creatina-ghost-1410' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-isolate-creatina-ghost-1410.jpg', 'Promocion Isolate, Creatina y Ghost', 0, NOW(), NOW());

-- Oferta Nitro Tech, Amino y Black Widow
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Oferta Nitro Tech, Amino y Black Widow', 'oferta-nitro-tech-amino-black-1690', 'GC-OFERTA-NITRO-TECH-AMINO-BLACK-1690', 'Go Center', 'Pack promocional Go Center. Incluye: Nitro Tech Whey Gold, Black Widow y Amino Energy. Costo de envio no incluido.', 1690.00, NULL, 10, 1, 1, 'Oferta Nitro Tech, Amino y Black Widow | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Nitro Tech Whey Gold, Black Widow y Amino Energy. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'oferta-nitro-tech-amino-black-1690' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/oferta-nitro-tech-amino-black-1690.jpg', 'Oferta Nitro Tech, Amino y Black Widow', 0, NOW(), NOW());

-- Combo Whey Inlabs y Nitro Tech
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Combo Whey Inlabs y Nitro Tech', 'combo-whey-inlabs-nitro-tech-1370', 'GC-COMBO-WHEY-INLABS-NITRO-TECH-1370', 'Go Center', 'Pack promocional Go Center. Incluye: Whey Inlabs y Nitro Tech Ripped. Costo de envio no incluido.', 1370.00, NULL, 10, 1, 1, 'Combo Whey Inlabs y Nitro Tech | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Whey Inlabs y Nitro Tech Ripped. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'combo-whey-inlabs-nitro-tech-1370' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/combo-whey-inlabs-nitro-tech-1370.jpg', 'Combo Whey Inlabs y Nitro Tech', 0, NOW(), NOW());

-- Especial Amino Energy y Ghost
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Especial Amino Energy y Ghost', 'especial-amino-energy-ghost-1020', 'GC-ESPECIAL-AMINO-ENERGY-GHOST-1020', 'Go Center', 'Pack promocional Go Center. Incluye: Amino Energy y Ghost Legend Pre Workout. Costo de envio no incluido.', 1020.00, NULL, 10, 1, 1, 'Especial Amino Energy y Ghost | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Amino Energy y Ghost Legend Pre Workout. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'especial-amino-energy-ghost-1020' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/especial-amino-energy-ghost-1020.jpg', 'Especial Amino Energy y Ghost', 0, NOW(), NOW());

-- Combo Completo Pro
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Combo Completo Pro', 'combo-completo-pro-1960', 'GC-COMBO-COMPLETO-PRO-1960', 'Go Center', 'Pack promocional Go Center. Incluye: Cbum Essential, creatina Dragon, Nitro Tech Whey Gold, BCAA Mutant Hardcore y multi mostrado en imagen. Costo de envio no incluido.', 1960.00, NULL, 10, 1, 1, 'Combo Completo Pro | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Cbum Essential, creatina Dragon, Nitro Tech Whey Gold, BCAA Mutant Hardcore y multi mostrado en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'combo-completo-pro-1960' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/combo-completo-pro-1960.jpg', 'Combo Completo Pro', 0, NOW(), NOW());

-- Combo Cell Tech, BCAA y Creatina
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Combo Cell Tech, BCAA y Creatina', 'combo-cell-tech-bcaa-creatina-1190', 'GC-COMBO-CELL-TECH-BCAA-CREATINA-1190', 'Go Center', 'Pack promocional Go Center. Incluye: Cell-Tech Creatine, BCAA y creatina. Costo de envio no incluido.', 1190.00, NULL, 10, 1, 1, 'Combo Cell Tech, BCAA y Creatina | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Cell-Tech Creatine, BCAA y creatina. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'combo-cell-tech-bcaa-creatina-1190' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/combo-cell-tech-bcaa-creatina-1190.jpg', 'Combo Cell Tech, BCAA y Creatina', 0, NOW(), NOW());

-- Combo Full Performance
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Combo Full Performance', 'combo-full-performance-1050', 'GC-COMBO-FULL-PERFORMANCE-1050', 'Go Center', 'Pack promocional Go Center. Incluye: Psychotic Black, shaker y Isolate Inlabs. Costo de envio no incluido.', 1050.00, NULL, 10, 1, 1, 'Combo Full Performance | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Psychotic Black, shaker y Isolate Inlabs. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'combo-full-performance-1050' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/combo-full-performance-1050.jpg', 'Combo Full Performance', 0, NOW(), NOW());

-- Combo Completo Pro Cbum, Amino y Beta
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Combo Completo Pro Cbum, Amino y Beta', 'combo-completo-pro-cbum-amino-beta-730', 'GC-COMBO-COMPLETO-PRO-CBUM-AMINO-BETA-730', 'Go Center', 'Pack promocional Go Center. Incluye: Cbum Performance, Amino Inlabs y beta alanina. Costo de envio no incluido.', 730.00, NULL, 10, 1, 1, 'Combo Completo Pro Cbum, Amino y Beta | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Cbum Performance, Amino Inlabs y beta alanina. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'combo-completo-pro-cbum-amino-beta-730' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/combo-completo-pro-cbum-amino-beta-730.jpg', 'Combo Completo Pro Cbum, Amino y Beta', 0, NOW(), NOW());

-- Pack Performance
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Pack Performance', 'pack-performance-1990', 'GC-PACK-PERFORMANCE-1990', 'Go Center', 'Pack promocional Go Center. Incluye: NO-Xplode, Serious Mass y Whey Inlabs. Costo de envio no incluido.', 1990.00, NULL, 10, 1, 1, 'Pack Performance | Go Center Suplementos', 'Pack promocional Go Center. Incluye: NO-Xplode, Serious Mass y Whey Inlabs. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'pack-performance-1990' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/pack-performance-1990.jpg', 'Pack Performance', 0, NOW(), NOW());

-- Pack Entrenamiento Joker Nitro y Creatina
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Pack Entrenamiento Joker Nitro y Creatina', 'pack-entrenamiento-joker-nitro-creatina-1640', 'GC-PACK-ENTRENAMIENTO-JOKER-NITRO-CREATINA-1640', 'Go Center', 'Pack promocional Go Center. Incluye: Joker, Nitro Tech y creatina Inlabs. Costo de envio no incluido.', 1640.00, NULL, 10, 1, 1, 'Pack Entrenamiento Joker Nitro y Creatina | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Joker, Nitro Tech y creatina Inlabs. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'pack-entrenamiento-joker-nitro-creatina-1640' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/pack-entrenamiento-joker-nitro-creatina-1640.jpg', 'Pack Entrenamiento Joker Nitro y Creatina', 0, NOW(), NOW());

-- Paquete Fitness EAA, Isolate y Creatina
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Paquete Fitness EAA, Isolate y Creatina', 'paquete-fitness-eaa-isolate-creatina-1130', 'GC-PAQUETE-FITNESS-EAA-ISOLATE-CREATINA-1130', 'Go Center', 'Pack promocional Go Center. Incluye: EAA Recovery, Isolate Inlabs y creatina Inlabs. Costo de envio no incluido.', 1130.00, NULL, 10, 1, 1, 'Paquete Fitness EAA, Isolate y Creatina | Go Center Suplementos', 'Pack promocional Go Center. Incluye: EAA Recovery, Isolate Inlabs y creatina Inlabs. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'paquete-fitness-eaa-isolate-creatina-1130' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/paquete-fitness-eaa-isolate-creatina-1130.jpg', 'Paquete Fitness EAA, Isolate y Creatina', 0, NOW(), NOW());

-- Paquete Pro Total
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Paquete Pro Total', 'paquete-pro-total-1440', 'GC-PAQUETE-PRO-TOTAL-1440', 'Go Center', 'Pack promocional Go Center. Incluye: Mr Veinz, Cbum Essential, Omega Inlabs, Whey Inlabs y productos mostrados en imagen. Costo de envio no incluido.', 1440.00, NULL, 10, 1, 1, 'Paquete Pro Total | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Mr Veinz, Cbum Essential, Omega Inlabs, Whey Inlabs y productos mostrados en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'paquete-pro-total-1440' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/paquete-pro-total-1440.jpg', 'Paquete Pro Total', 0, NOW(), NOW());

-- Pack Activo
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Pack Activo', 'pack-activo-1070', 'GC-PACK-ACTIVO-1070', 'Go Center', 'Pack promocional Go Center. Incluye: BCAA Inlabs, Mutant Whey y Stimul8. Costo de envio no incluido.', 1070.00, NULL, 10, 1, 1, 'Pack Activo | Go Center Suplementos', 'Pack promocional Go Center. Incluye: BCAA Inlabs, Mutant Whey y Stimul8. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'pack-activo-1070' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/pack-activo-1070.jpg', 'Pack Activo', 0, NOW(), NOW());

-- Promocion Pro
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promocion Pro', 'promocion-pro-900', 'GC-PROMOCION-PRO-900', 'Go Center', 'Pack promocional Go Center. Incluye: Clean Whey Protein y Psychopath. Costo de envio no incluido.', 900.00, NULL, 10, 1, 1, 'Promocion Pro | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Clean Whey Protein y Psychopath. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promocion-pro-900' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promocion-pro-900.jpg', 'Promocion Pro', 0, NOW(), NOW());

-- Paquete Fitness Mutant, Psychotic y Creatina
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Paquete Fitness Mutant, Psychotic y Creatina', 'paquete-fitness-mutant-psychotic-creatina-1270', 'GC-PAQUETE-FITNESS-MUTANT-PSYCHOTIC-CREATINA-1270', 'Go Center', 'Pack promocional Go Center. Incluye: Mutant Whey, Psychotic y creatina Inlabs. Costo de envio no incluido.', 1270.00, NULL, 10, 1, 1, 'Paquete Fitness Mutant, Psychotic y Creatina | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Mutant Whey, Psychotic y creatina Inlabs. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'paquete-fitness-mutant-psychotic-creatina-1270' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/paquete-fitness-mutant-psychotic-creatina-1270.jpg', 'Paquete Fitness Mutant, Psychotic y Creatina', 0, NOW(), NOW());

-- Paquete Fitness Gold Standard
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Paquete Fitness Gold Standard', 'paquete-fitness-gold-standard-1250', 'GC-PAQUETE-FITNESS-GOLD-STANDARD-1250', 'Go Center', 'Pack promocional Go Center. Incluye: Whey Inlabs, Gold Standard y creatina Inlabs. Costo de envio no incluido.', 1250.00, NULL, 10, 1, 1, 'Paquete Fitness Gold Standard | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Whey Inlabs, Gold Standard y creatina Inlabs. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'paquete-fitness-gold-standard-1250' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/paquete-fitness-gold-standard-1250.jpg', 'Paquete Fitness Gold Standard', 0, NOW(), NOW());

-- Pack Avanzado Amino y Gold Standard
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Pack Avanzado Amino y Gold Standard', 'pack-avanzado-amino-gold-standard-1270', 'GC-PACK-AVANZADO-AMINO-GOLD-STANDARD-1270', 'Go Center', 'Pack promocional Go Center. Incluye: Amino Energy, Gold Standard Whey, shaker y Psychotic. Costo de envio no incluido.', 1270.00, NULL, 10, 1, 1, 'Pack Avanzado Amino y Gold Standard | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Amino Energy, Gold Standard Whey, shaker y Psychotic. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'pack-avanzado-amino-gold-standard-1270' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/pack-avanzado-amino-gold-standard-1270.jpg', 'Pack Avanzado Amino y Gold Standard', 0, NOW(), NOW());

-- Paquete Completo Veinz y Psychotic
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Paquete Completo Veinz y Psychotic', 'paquete-completo-veinz-psychotic-1130', 'GC-PAQUETE-COMPLETO-VEINZ-PSYCHOTIC-1130', 'Go Center', 'Pack promocional Go Center. Incluye: Mr Veinz y Psychotic. Costo de envio no incluido.', 1130.00, NULL, 10, 1, 1, 'Paquete Completo Veinz y Psychotic | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Mr Veinz y Psychotic. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'paquete-completo-veinz-psychotic-1130' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/paquete-completo-veinz-psychotic-1130.jpg', 'Paquete Completo Veinz y Psychotic', 0, NOW(), NOW());

-- Paquete Completo Amino y Whey
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Paquete Completo Amino y Whey', 'paquete-completo-amino-whey-1340', 'GC-PAQUETE-COMPLETO-AMINO-WHEY-1340', 'Go Center', 'Pack promocional Go Center. Incluye: Amino Inlabs, Omega Inlabs, Whey Inlabs y Gold Standard. Costo de envio no incluido.', 1340.00, NULL, 10, 1, 1, 'Paquete Completo Amino y Whey | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Amino Inlabs, Omega Inlabs, Whey Inlabs y Gold Standard. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'paquete-completo-amino-whey-1340' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/paquete-completo-amino-whey-1340.jpg', 'Paquete Completo Amino y Whey', 0, NOW(), NOW());

-- Pack Avanzado BCAA y Whey
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Pack Avanzado BCAA y Whey', 'pack-avanzado-bcaa-whey-930', 'GC-PACK-AVANZADO-BCAA-WHEY-930', 'Go Center', 'Pack promocional Go Center. Incluye: BCAA Inlabs, Whey Inlabs y shaker. Costo de envio no incluido.', 930.00, NULL, 10, 1, 1, 'Pack Avanzado BCAA y Whey | Go Center Suplementos', 'Pack promocional Go Center. Incluye: BCAA Inlabs, Whey Inlabs y shaker. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'pack-avanzado-bcaa-whey-930' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/pack-avanzado-bcaa-whey-930.jpg', 'Pack Avanzado BCAA y Whey', 0, NOW(), NOW());

-- Pack Avanzado Mutant Mass
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Pack Avanzado Mutant Mass', 'pack-avanzado-mutant-mass-1490', 'GC-PACK-AVANZADO-MUTANT-MASS-1490', 'Go Center', 'Pack promocional Go Center. Incluye: shaker, glutamina, Mutant Mass, The Curse y creatina Inlabs. Costo de envio no incluido.', 1490.00, NULL, 10, 1, 1, 'Pack Avanzado Mutant Mass | Go Center Suplementos', 'Pack promocional Go Center. Incluye: shaker, glutamina, Mutant Mass, The Curse y creatina Inlabs. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'pack-avanzado-mutant-mass-1490' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/pack-avanzado-mutant-mass-1490.jpg', 'Pack Avanzado Mutant Mass', 0, NOW(), NOW());

-- Pack Disponible
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Pack Disponible', 'pack-disponible-2190', 'GC-PACK-DISPONIBLE-2190', 'Go Center', 'Pack promocional Go Center. Incluye: Whey Inlabs, productos de soporte y creatina mostrados en imagen. Costo de envio no incluido.', 2190.00, NULL, 10, 1, 1, 'Pack Disponible | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Whey Inlabs, productos de soporte y creatina mostrados en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'pack-disponible-2190' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/pack-disponible-2190.jpg', 'Pack Disponible', 0, NOW(), NOW());

-- Pack Completo Gold Standard y Cbum
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Pack Completo Gold Standard y Cbum', 'pack-completo-gold-standard-cbum-1400', 'GC-PACK-COMPLETO-GOLD-STANDARD-CBUM-1400', 'Go Center', 'Pack promocional Go Center. Incluye: creatina, Gold Standard Whey, Cbum Essential y pre workout. Costo de envio no incluido.', 1400.00, NULL, 10, 1, 1, 'Pack Completo Gold Standard y Cbum | Go Center Suplementos', 'Pack promocional Go Center. Incluye: creatina, Gold Standard Whey, Cbum Essential y pre workout. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'pack-completo-gold-standard-cbum-1400' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/pack-completo-gold-standard-cbum-1400.jpg', 'Pack Completo Gold Standard y Cbum', 0, NOW(), NOW());

-- Black Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Black Pack', 'black-pack-1820', 'GC-BLACK-PACK-1820', 'Go Center', 'Pack promocional Go Center. Incluye: Isolate, creatina, glutamina, Psychotic, BCAAS y regalo mostrado en imagen. Costo de envio no incluido.', 1820.00, NULL, 10, 1, 1, 'Black Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Isolate, creatina, glutamina, Psychotic, BCAAS y regalo mostrado en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'black-pack-1820' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/black-pack-1820.jpg', 'Black Pack', 0, NOW(), NOW());

-- Fitness Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Fitness Pack', 'fitness-pack-2950', 'GC-FITNESS-PACK-2950', 'Go Center', 'Pack promocional Go Center. Incluye: Nitro Tech, creatina, amino, Nitraflex, glutamina y productos mostrados en imagen. Costo de envio no incluido.', 2950.00, NULL, 10, 1, 1, 'Fitness Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Nitro Tech, creatina, amino, Nitraflex, glutamina y productos mostrados en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'fitness-pack-2950' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/fitness-pack-2950.jpg', 'Fitness Pack', 0, NOW(), NOW());

-- Paquete Pro
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Paquete Pro', 'paquete-pro-2130', 'GC-PAQUETE-PRO-2130', 'Go Center', 'Pack promocional Go Center. Incluye: Whey Inlabs, creatina, shaker, glutamina y Breach. Costo de envio no incluido.', 2130.00, NULL, 10, 1, 1, 'Paquete Pro | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Whey Inlabs, creatina, shaker, glutamina y Breach. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'paquete-pro-2130' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/paquete-pro-2130.jpg', 'Paquete Pro', 0, NOW(), NOW());

-- Power Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Power Pack', 'power-pack-2380', 'GC-POWER-PACK-2380', 'Go Center', 'Pack promocional Go Center. Incluye: Isolate, creatina, amino, pre workout, Stimul8 y productos mostrados en imagen. Costo de envio no incluido.', 2380.00, NULL, 10, 1, 1, 'Power Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Isolate, creatina, amino, pre workout, Stimul8 y productos mostrados en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'power-pack-2380' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/power-pack-2380.jpg', 'Power Pack', 0, NOW(), NOW());

-- Iron Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Iron Pack', 'iron-pack-2790', 'GC-IRON-PACK-2790', 'Go Center', 'Pack promocional Go Center. Incluye: combo completo de suplementos mostrados en imagen, con regalo incluido. Costo de envio no incluido.', 2790.00, NULL, 10, 1, 1, 'Iron Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: combo completo de suplementos mostrados en imagen, con regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'iron-pack-2790' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/iron-pack-2790.jpg', 'Iron Pack', 0, NOW(), NOW());

-- New Promo
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'New Promo', 'new-promo-1750', 'GC-NEW-PROMO-1750', 'Go Center', 'Pack promocional Go Center. Incluye: Isolate, creatina, amino, BCAAS y productos mostrados en imagen. Costo de envio no incluido.', 1750.00, NULL, 10, 1, 1, 'New Promo | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Isolate, creatina, amino, BCAAS y productos mostrados en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'new-promo-1750' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/new-promo-1750.jpg', 'New Promo', 0, NOW(), NOW());

-- Powerful Blow Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Powerful Blow Pack', 'powerful-blow-pack-1910', 'GC-POWERFUL-BLOW-PACK-1910', 'Go Center', 'Pack promocional Go Center. Incluye: pre workout, Total War, Nitraflex, Psychotic y Mr Veinz. Costo de envio no incluido.', 1910.00, NULL, 10, 1, 1, 'Powerful Blow Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: pre workout, Total War, Nitraflex, Psychotic y Mr Veinz. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'powerful-blow-pack-1910' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/powerful-blow-pack-1910.jpg', 'Powerful Blow Pack', 0, NOW(), NOW());

-- Promo Vigente
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Promo Vigente', 'promo-vigente-1730', 'GC-PROMO-VIGENTE-1730', 'Go Center', 'Pack promocional Go Center. Incluye: Combat, shaker, C4, Total War y Stimul8. Costo de envio no incluido.', 1730.00, NULL, 10, 1, 1, 'Promo Vigente | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Combat, shaker, C4, Total War y Stimul8. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'promo-vigente-1730' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/promo-vigente-1730.jpg', 'Promo Vigente', 0, NOW(), NOW());

-- Nitro Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Nitro Pack', 'nitro-pack-1620', 'GC-NITRO-PACK-1620', 'Go Center', 'Pack promocional Go Center. Incluye: Nitro Tech, creatina y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 1620.00, NULL, 10, 1, 1, 'Nitro Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Nitro Tech, creatina y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'nitro-pack-1620' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/nitro-pack-1620.jpg', 'Nitro Pack', 0, NOW(), NOW());

-- Go Prime Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Go Prime Pack', 'go-prime-pack-1550', 'GC-GO-PRIME-PACK-1550', 'Go Center', 'Pack promocional Go Center. Incluye: glutamina, arginina, creatina, citrulina y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 1550.00, NULL, 10, 1, 1, 'Go Prime Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: glutamina, arginina, creatina, citrulina y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'go-prime-pack-1550' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/go-prime-pack-1550.jpg', 'Go Prime Pack', 0, NOW(), NOW());

-- Powder Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Powder Pack', 'powder-pack-2390', 'GC-POWDER-PACK-2390', 'Go Center', 'Pack promocional Go Center. Incluye: whey, creatina, multivitaminico, glutamina, shaker y productos mostrados en imagen. Costo de envio no incluido.', 2390.00, NULL, 10, 1, 1, 'Powder Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: whey, creatina, multivitaminico, glutamina, shaker y productos mostrados en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'powder-pack-2390' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/powder-pack-2390.jpg', 'Powder Pack', 0, NOW(), NOW());

-- Energy Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Energy Pack', 'energy-pack-1930', 'GC-ENERGY-PACK-1930', 'Go Center', 'Pack promocional Go Center. Incluye: whey, creatina, amino, Psychotic y productos mostrados en imagen. Costo de envio no incluido.', 1930.00, NULL, 10, 1, 1, 'Energy Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: whey, creatina, amino, Psychotic y productos mostrados en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'energy-pack-1930' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/energy-pack-1930.jpg', 'Energy Pack', 0, NOW(), NOW());

-- The Complete Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'The Complete Pack', 'the-complete-pack-1950', 'GC-THE-COMPLETE-PACK-1950', 'Go Center', 'Pack promocional Go Center. Incluye: Isolate, shaker, Total War, Mutant, C4 y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 1950.00, NULL, 10, 1, 1, 'The Complete Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Isolate, shaker, Total War, Mutant, C4 y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'the-complete-pack-1950' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/the-complete-pack-1950.jpg', 'The Complete Pack', 0, NOW(), NOW());

-- Recovery & Power Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Recovery & Power Pack', 'recovery-power-pack-1730', 'GC-RECOVERY-POWER-PACK-1730', 'Go Center', 'Pack promocional Go Center. Incluye: Whey Inlabs, creatina, glutamina, amino, BCAAS y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 1730.00, NULL, 10, 1, 1, 'Recovery & Power Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Whey Inlabs, creatina, glutamina, amino, BCAAS y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'recovery-power-pack-1730' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/recovery-power-pack-1730.jpg', 'Recovery & Power Pack', 0, NOW(), NOW());

-- New Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'New Pack', 'new-pack-2550', 'GC-NEW-PACK-2550', 'Go Center', 'Pack promocional Go Center. Incluye: whey, glutamina, amino, pre workout y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 2550.00, NULL, 10, 1, 1, 'New Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: whey, glutamina, amino, pre workout y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'new-pack-2550' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/new-pack-2550.jpg', 'New Pack', 0, NOW(), NOW());

-- Total Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Total Pack', 'total-pack-2010', 'GC-TOTAL-PACK-2010', 'Go Center', 'Pack promocional Go Center. Incluye: combo activo de suplementos mostrados en imagen, con regalo incluido. Costo de envio no incluido.', 2010.00, NULL, 10, 1, 1, 'Total Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: combo activo de suplementos mostrados en imagen, con regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'total-pack-2010' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/total-pack-2010.jpg', 'Total Pack', 0, NOW(), NOW());

-- Gainz Box Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Gainz Box Pack', 'gainz-box-pack-2250', 'GC-GAINZ-BOX-PACK-2250', 'Go Center', 'Pack promocional Go Center. Incluye: Isopure, carnitina, lipo, omega, C4 y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 2250.00, NULL, 10, 1, 1, 'Gainz Box Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Isopure, carnitina, lipo, omega, C4 y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'gainz-box-pack-2250' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/gainz-box-pack-2250.jpg', 'Gainz Box Pack', 0, NOW(), NOW());

-- Mass Max Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Mass Max Pack', 'mass-max-pack-1830', 'GC-MASS-MAX-PACK-1830', 'Go Center', 'Pack promocional Go Center. Incluye: Mass Infusion, Whey Inlabs, creatina y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 1830.00, NULL, 10, 1, 1, 'Mass Max Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Mass Infusion, Whey Inlabs, creatina y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'mass-max-pack-1830' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/mass-max-pack-1830.jpg', 'Mass Max Pack', 0, NOW(), NOW());

-- Nuevo Combo
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Nuevo Combo', 'nuevo-combo-1710', 'GC-NUEVO-COMBO-1710', 'Go Center', 'Pack promocional Go Center. Incluye: creatina, shaker, glutamina, pre workout y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 1710.00, NULL, 10, 1, 1, 'Nuevo Combo | Go Center Suplementos', 'Pack promocional Go Center. Incluye: creatina, shaker, glutamina, pre workout y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'nuevo-combo-1710' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/nuevo-combo-1710.jpg', 'Nuevo Combo', 0, NOW(), NOW());

-- Maximum Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Maximum Pack', 'maximum-pack-1630', 'GC-MAXIMUM-PACK-1630', 'Go Center', 'Pack promocional Go Center. Incluye: creatina, cafeina, Psychotic y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 1630.00, NULL, 10, 1, 1, 'Maximum Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: creatina, cafeina, Psychotic y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'maximum-pack-1630' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/maximum-pack-1630.jpg', 'Maximum Pack', 0, NOW(), NOW());

-- Combo Wellness
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Combo Wellness', 'combo-wellness-2630', 'GC-COMBO-WELLNESS-2630', 'Go Center', 'Pack promocional Go Center. Incluye: Isolate, creatina, omega, shaker y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 2630.00, NULL, 10, 1, 1, 'Combo Wellness | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Isolate, creatina, omega, shaker y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'combo-wellness-2630' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/combo-wellness-2630.jpg', 'Combo Wellness', 0, NOW(), NOW());

-- Kit Performance
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Kit Performance', 'kit-performance-1860', 'GC-KIT-PERFORMANCE-1860', 'Go Center', 'Pack promocional Go Center. Incluye: creatina, BCAAS, Psychotic, proteina y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 1860.00, NULL, 10, 1, 1, 'Kit Performance | Go Center Suplementos', 'Pack promocional Go Center. Incluye: creatina, BCAAS, Psychotic, proteina y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'kit-performance-1860' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/kit-performance-1860.jpg', 'Kit Performance', 0, NOW(), NOW());

-- Strong Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Strong Pack', 'strong-pack-2490', 'GC-STRONG-PACK-2490', 'Go Center', 'Pack promocional Go Center. Incluye: Whey Protein, creatina, omega y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 2490.00, NULL, 10, 1, 1, 'Strong Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Whey Protein, creatina, omega y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'strong-pack-2490' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/strong-pack-2490.jpg', 'Strong Pack', 0, NOW(), NOW());

-- Magnum Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Magnum Pack', 'magnum-pack-2990', 'GC-MAGNUM-PACK-2990', 'Go Center', 'Pack promocional Go Center. Incluye: whey, Nitro Tech, pre workout, creatina y productos mostrados en imagen. Costo de envio no incluido.', 2990.00, NULL, 10, 1, 1, 'Magnum Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: whey, Nitro Tech, pre workout, creatina y productos mostrados en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'magnum-pack-2990' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/magnum-pack-2990.jpg', 'Magnum Pack', 0, NOW(), NOW());

-- Super Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Super Pack', 'super-pack-2720', 'GC-SUPER-PACK-2720', 'Go Center', 'Pack promocional Go Center. Incluye: Nitro Tech, Isopure, creatina y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 2720.00, NULL, 10, 1, 1, 'Super Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: Nitro Tech, Isopure, creatina y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'super-pack-2720' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/super-pack-2720.jpg', 'Super Pack', 0, NOW(), NOW());

-- Standard Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Standard Pack', 'standard-pack-2460', 'GC-STANDARD-PACK-2460', 'Go Center', 'Pack promocional Go Center. Incluye: whey, creatina y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 2460.00, NULL, 10, 1, 1, 'Standard Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: whey, creatina y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'standard-pack-2460' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/standard-pack-2460.jpg', 'Standard Pack', 0, NOW(), NOW());

-- Nuevo Combo Isolate
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Nuevo Combo Isolate', 'nuevo-combo-isolate-2370', 'GC-NUEVO-COMBO-ISOLATE-2370', 'Go Center', 'Pack promocional Go Center. Incluye: ISO100, Isolate, creatina y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 2370.00, NULL, 10, 1, 1, 'Nuevo Combo Isolate | Go Center Suplementos', 'Pack promocional Go Center. Incluye: ISO100, Isolate, creatina y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'nuevo-combo-isolate-2370' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/nuevo-combo-isolate-2370.jpg', 'Nuevo Combo Isolate', 0, NOW(), NOW());

-- Fitness Pro Pack
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Fitness Pro Pack', 'fitness-pro-pack-1350', 'GC-FITNESS-PRO-PACK-1350', 'Go Center', 'Pack promocional Go Center. Incluye: ISO Lean, creatina, Amino Energy y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', 1350.00, NULL, 10, 1, 1, 'Fitness Pro Pack | Go Center Suplementos', 'Pack promocional Go Center. Incluye: ISO Lean, creatina, Amino Energy y productos mostrados en imagen; regalo incluido. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'fitness-pro-pack-1350' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/fitness-pro-pack-1350.jpg', 'Fitness Pro Pack', 0, NOW(), NOW());

-- Paquete Performance
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Paquete Performance', 'paquete-performance-1770', 'GC-PAQUETE-PERFORMANCE-1770', 'Go Center', 'Pack promocional Go Center. Incluye: creatina GAT, Gold Standard Whey y Best BCAA. Costo de envio no incluido.', 1770.00, NULL, 10, 1, 1, 'Paquete Performance | Go Center Suplementos', 'Pack promocional Go Center. Incluye: creatina GAT, Gold Standard Whey y Best BCAA. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'paquete-performance-1770' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/paquete-performance-1770.jpg', 'Paquete Performance', 0, NOW(), NOW());

-- Paquete Evolucion
INSERT INTO products (`category_id`, `name`, `slug`, `sku`, `brand`, `description`, `price`, `compare_at_price`, `stock`, `featured`, `active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(@category_id, 'Paquete Evolucion', 'paquete-evolucion-2190', 'GC-PAQUETE-EVOLUCION-2190', 'Go Center', 'Pack promocional Go Center. Incluye: ISO100, amino, pre workout y productos mostrados en imagen. Costo de envio no incluido.', 2190.00, NULL, 10, 1, 1, 'Paquete Evolucion | Go Center Suplementos', 'Pack promocional Go Center. Incluye: ISO100, amino, pre workout y productos mostrados en imagen. Costo de envio no incluido.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_id` = VALUES(`category_id`),
  `name` = VALUES(`name`),
  `sku` = VALUES(`sku`),
  `brand` = VALUES(`brand`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `compare_at_price` = VALUES(`compare_at_price`),
  `stock` = VALUES(`stock`),
  `featured` = VALUES(`featured`),
  `active` = VALUES(`active`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

SET @product_id := (SELECT id FROM products WHERE slug = 'paquete-evolucion-2190' LIMIT 1);
DELETE FROM product_images WHERE product_id = @product_id;
INSERT INTO product_images (`product_id`, `path`, `alt`, `sort_order`, `created_at`, `updated_at`) VALUES
(@product_id, 'assets/gocenter/products/paquete-evolucion-2190.jpg', 'Paquete Evolucion', 0, NOW(), NOW());

COMMIT;
