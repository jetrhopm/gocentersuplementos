<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\CartService;
use App\Services\CatalogBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class StoreFlowTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_home_returns_successfully(): void
    {
        $this->get('/')->assertOk()->assertSee('Go Center Suplementos');
    }

    public function test_admin_can_login(): void
    {
        $this->post(route('admin.authenticate'), [
            'email' => 'admin@local.test',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_customer_can_create_transfer_order_without_account(): void
    {
        $product = Product::where('stock', '>', 0)->firstOrFail();

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertRedirect();

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Juan Perez',
            'customer_email' => 'juan@example.com',
            'customer_phone' => '5512345678',
            'street' => 'Av Reforma',
            'external_number' => '123',
            'neighborhood' => 'Centro',
            'city' => 'Cuauhtemoc',
            'state' => 'CDMX',
            'postal_code' => '06000',
            'payment_method' => 'transferencia',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'customer_email' => 'juan@example.com',
            'payment_method' => 'transferencia',
            'status' => 'pendiente_transferencia',
        ]);
    }

    public function test_shipping_cost_is_removed_when_subtotal_reaches_free_shipping_minimum(): void
    {
        config([
            'services.store.shipping_cost' => 150,
            'services.store.free_shipping_from' => 999,
        ]);

        $product = Product::where('stock', '>', 0)->firstOrFail();
        $product->update(['price' => 500, 'stock' => 10]);

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertRedirect();

        $totals = app(CartService::class)->totals();

        $this->assertSame(150.0, $totals['shipping']);
        $this->assertFalse($totals['has_free_shipping']);

        session()->forget(['cart.items', 'cart.coupon']);
        $product->update(['price' => 999]);

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertRedirect();

        $totals = app(CartService::class)->totals();

        $this->assertSame(0.0, $totals['shipping']);
        $this->assertTrue($totals['has_free_shipping']);
    }

    public function test_coupon_can_be_removed_without_reapplying_it(): void
    {
        $product = Product::where('stock', '>', 0)->firstOrFail();
        $product->update(['price' => 1000, 'stock' => 10]);

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertRedirect();

        $this->postJson(route('cart.coupon.apply'), [
            'coupon' => 'BIENVENIDA10',
        ])->assertOk()->assertJsonPath('totals.discount', '100.00');

        $this->deleteJson(route('cart.coupon.remove'))
            ->assertOk()
            ->assertJsonPath('message', 'Cupon retirado.')
            ->assertJsonPath('totals.discount', '0.00');

        $this->assertNull(session('cart.coupon'));
    }

    public function test_cart_can_be_cleared(): void
    {
        $product = Product::where('stock', '>', 0)->firstOrFail();

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertRedirect();

        session(['cart.coupon' => 'BIENVENIDA10']);

        $this->delete(route('cart.clear'))
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('status', 'Carrito vaciado.');

        $this->assertSame([], session('cart.items', []));
        $this->assertNull(session('cart.coupon'));
    }

    public function test_clip_legacy_webhook_marks_order_as_paid(): void
    {
        $order = Order::create([
            'folio' => Order::makeFolio(),
            'customer_name' => 'Juan Perez',
            'customer_email' => 'juan@example.com',
            'customer_phone' => '5512345678',
            'street' => 'Av Reforma',
            'external_number' => '123',
            'neighborhood' => 'Centro',
            'city' => 'Cuauhtemoc',
            'state' => 'CDMX',
            'postal_code' => '06000',
            'subtotal' => 1000,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 1000,
            'payment_method' => 'clip',
            'status' => Order::STATUS_PENDING_CLIP,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'method' => 'clip',
            'provider' => 'clip',
            'status' => 'pending',
            'amount' => 1000,
            'currency' => 'MXN',
            'external_reference' => $order->folio,
            'payment_request_id' => 'clip_req_123',
        ]);

        $this->postJson(route('webhooks.clip.legacy'), [
            'event_type' => 'REQUEST_COMPLETED',
            'id' => 'clip_evt_123',
            'payment_detail' => [
                'merch_inv_id' => $order->folio,
                'receipt_no' => 'RCPT-123',
            ],
            'payment_request_detail' => [
                'merch_inv_id' => $order->folio,
            ],
        ])->assertOk()->assertJsonPath('ok', true);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_PAID,
        ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => 'paid',
            'receipt_no' => 'RCPT-123',
        ]);
    }

    public function test_clip_webhook_accepts_unsigned_diagnostic_ping_without_processing_payment(): void
    {
        config(['services.clip.webhook_secret' => 'secret-for-signature']);

        $this->postJson(route('webhooks.clip'), [])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('provider', 'clip')
            ->assertJsonPath('diagnostic', true);

        $this->assertDatabaseHas('payment_webhook_logs', [
            'provider' => 'clip',
            'status' => 'diagnostic_ping',
            'signature_valid' => false,
            'response_status' => 200,
        ]);
    }

    public function test_clip_webhook_status_route_is_available_for_provider_validation(): void
    {
        $this->get(route('webhooks.clip.status'))
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('provider', 'clip')
            ->assertJsonPath('message', 'Webhook activo.');
    }

    public function test_clip_webhook_rejects_unsigned_payment_payload(): void
    {
        config(['services.clip.webhook_secret' => 'secret-for-signature']);

        $this->postJson(route('webhooks.clip'), [
            'event_type' => 'REQUEST_COMPLETED',
            'payment_detail' => [
                'merch_inv_id' => 'GYM-TEST-UNSIGNED',
            ],
            'payment_request_detail' => [
                'id' => 'clip_req_unsigned',
                'merch_inv_id' => 'GYM-TEST-UNSIGNED',
            ],
        ])->assertUnauthorized()->assertJsonPath('ok', false);

        $this->assertDatabaseHas('payment_webhook_logs', [
            'provider' => 'clip',
            'status' => 'invalid_signature',
            'signature_valid' => false,
            'response_status' => 401,
        ]);
    }

    public function test_limited_admin_cannot_reject_paid_clip_order(): void
    {
        $admin = User::where('email', 'admin@local.test')->firstOrFail();

        $order = Order::create([
            'folio' => Order::makeFolio(),
            'customer_name' => 'Juan Perez',
            'customer_email' => 'juan@example.com',
            'customer_phone' => '5512345678',
            'street' => 'Av Reforma',
            'external_number' => '123',
            'neighborhood' => 'Centro',
            'city' => 'Cuauhtemoc',
            'state' => 'CDMX',
            'postal_code' => '06000',
            'subtotal' => 1000,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 1000,
            'payment_method' => 'clip',
            'status' => Order::STATUS_PAID,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'method' => 'clip',
            'provider' => 'clip',
            'status' => 'paid',
            'amount' => 1000,
            'currency' => 'MXN',
            'external_reference' => $order->folio,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.status', $order), [
                'status' => Order::STATUS_REJECTED,
                'rejection_reason' => 'No registrado',
            ])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_PAID,
        ]);
    }

    public function test_super_admin_can_create_admin_user(): void
    {
        $superAdmin = User::where('email', 'superadmin@local.test')->firstOrFail();

        $this->actingAs($superAdmin)
            ->post(route('admin.users.store'), [
                'name' => 'Editor Catalogo',
                'email' => 'editor@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => User::ROLE_ADMIN,
                'active' => '1',
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'email' => 'editor@example.com',
            'role' => User::ROLE_ADMIN,
            'active' => true,
        ]);
    }

    public function test_limited_admin_cannot_manage_admin_users(): void
    {
        $admin = User::where('email', 'admin@local.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_activate_inactive_product(): void
    {
        $admin = User::where('email', 'admin@local.test')->firstOrFail();
        $product = Product::with('category')->firstOrFail();
        $product->update(['active' => false]);

        $this->actingAs($admin)
            ->put(route('admin.products.update', $product), [
                'category_id' => $product->category_id,
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'brand' => $product->brand,
                'description' => $product->description,
                'price' => $product->price,
                'compare_at_price' => $product->compare_at_price,
                'stock' => $product->stock,
                'featured' => $product->featured ? '1' : '0',
                'active' => '1',
                'meta_title' => $product->meta_title,
                'meta_description' => $product->meta_description,
            ])
            ->assertRedirect(route('admin.products.edit', $product))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'active' => true,
        ]);
    }

    public function test_clip_error_page_links_to_signed_received_order(): void
    {
        $order = Order::create([
            'folio' => Order::makeFolio(),
            'customer_name' => 'Juan Perez',
            'customer_email' => 'juan@example.com',
            'customer_phone' => '5512345678',
            'street' => 'Av Reforma',
            'external_number' => '123',
            'neighborhood' => 'Centro',
            'city' => 'Cuauhtemoc',
            'state' => 'CDMX',
            'postal_code' => '06000',
            'subtotal' => 1000,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 1000,
            'payment_method' => 'clip',
            'status' => Order::STATUS_PENDING_CLIP,
        ]);

        $response = $this->get(route('checkout.clip.error', ['folio' => $order->folio]));

        $response->assertOk()
            ->assertSee('signature=', false)
            ->assertSee('/checkout/pedido-recibido/'.$order->getRouteKey(), false);
    }

    public function test_transfer_received_page_shows_numeric_reference_and_optional_bank_reference(): void
    {
        $order = Order::create([
            'folio' => Order::makeFolio(),
            'customer_name' => 'Juan Perez',
            'customer_email' => 'juan@example.com',
            'customer_phone' => '5512345678',
            'street' => 'Av Reforma',
            'external_number' => '123',
            'neighborhood' => 'Centro',
            'city' => 'Cuauhtemoc',
            'state' => 'CDMX',
            'postal_code' => '06000',
            'subtotal' => 1000,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 1000,
            'payment_method' => 'transferencia',
            'status' => Order::STATUS_PENDING_TRANSFER,
        ]);

        $response = $this->get(URL::signedRoute('checkout.received', $order));

        $response->assertOk()
            ->assertSee($order->transferNumericReference())
            ->assertSee('Datos del comprador')
            ->assertSee($order->customer_name)
            ->assertSee($order->customer_email)
            ->assertSee($order->customer_phone)
            ->assertSee($order->street)
            ->assertSee($order->neighborhood)
            ->assertSee('nos ayuda a identificar tu pago mas rapido')
            ->assertSee('Referencia usada o generada por tu banco (opcional)')
            ->assertDontSee('required', false);
    }

    public function test_postal_code_endpoint_returns_settlements(): void
    {
        $this->getJson(route('postal-codes.show', ['postalCode' => '81200']))
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('state', 'Sinaloa')
            ->assertJsonPath('city', 'Los Mochis')
            ->assertJsonFragment(['name' => 'Centro']);
    }

    public function test_order_lookup_uses_get_and_does_not_require_csrf_session(): void
    {
        $order = $this->makePendingClipOrder();

        $this->get(route('orders.lookup.result', [
            'folio' => $order->folio,
            'contact' => $order->customer_email,
        ]))
            ->assertOk()
            ->assertSee($order->folio);
    }

    public function test_cancelling_clip_keeps_order_pending(): void
    {
        $order = $this->makePendingClipOrder();

        $this->get(URL::signedRoute('checkout.clip.cancelled', ['order' => $order, 'folio' => $order->folio]))
            ->assertOk();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_PENDING_CLIP,
        ]);
    }

    public function test_pending_order_shows_pay_button(): void
    {
        $order = $this->makePendingClipOrder();

        $this->get(URL::signedRoute('orders.public.show', $order))
            ->assertOk()
            ->assertSee('Pagar con Clip');
    }

    public function test_admin_can_send_payment_reminder(): void
    {
        \Illuminate\Support\Facades\Mail::fake();
        $admin = User::where('email', 'admin@local.test')->firstOrFail();
        $order = $this->makePendingClipOrder();

        $this->actingAs($admin)
            ->post(route('admin.orders.payment-reminder', $order))
            ->assertSessionHas('status');

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\PaymentReminderMail::class);
    }

    public function test_payment_reminder_blocked_for_paid_order(): void
    {
        \Illuminate\Support\Facades\Mail::fake();
        $admin = User::where('email', 'admin@local.test')->firstOrFail();
        $order = $this->makePendingClipOrder();
        $order->update(['status' => Order::STATUS_PAID]);

        $this->actingAs($admin)
            ->post(route('admin.orders.payment-reminder', $order))
            ->assertSessionHasErrors('reminder');

        \Illuminate\Support\Facades\Mail::assertNothingSent();
    }

    public function test_super_admin_can_delete_order_and_restore_discounted_stock(): void
    {
        $superAdmin = User::where('email', 'superadmin@local.test')->firstOrFail();
        $product = Product::where('stock', '>', 0)->firstOrFail();
        $product->update(['stock' => 7]);

        $order = Order::create([
            'folio' => Order::makeFolio(),
            'customer_name' => 'Juan Perez',
            'customer_email' => 'juan@example.com',
            'customer_phone' => '5512345678',
            'street' => 'Av Reforma',
            'external_number' => '123',
            'neighborhood' => 'Centro',
            'city' => 'Cuauhtemoc',
            'state' => 'CDMX',
            'postal_code' => '06000',
            'subtotal' => 300,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 300,
            'payment_method' => 'clip',
            'status' => Order::STATUS_PAID,
            'stock_discounted_at' => now(),
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 100,
            'quantity' => 3,
            'total' => 300,
        ]);

        $this->actingAs($superAdmin)
            ->delete(route('admin.orders.destroy', $order))
            ->assertRedirect(route('admin.orders.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertSame(10, $product->refresh()->stock);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'order_deleted_restore',
            'quantity' => 3,
        ]);
    }

    public function test_limited_admin_cannot_delete_orders(): void
    {
        $admin = User::where('email', 'admin@local.test')->firstOrFail();
        $order = $this->makePendingClipOrder();

        $this->actingAs($admin)
            ->delete(route('admin.orders.destroy', $order))
            ->assertForbidden();

        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    public function test_super_admin_can_download_catalog_backup(): void
    {
        $superAdmin = User::where('email', 'superadmin@local.test')->firstOrFail();
        $categoryIds = \App\Models\Category::query()->limit(2)->pluck('id')->all();

        $response = $this->actingAs($superAdmin)
            ->post(route('admin.backups.catalog.download'), [
                'category_ids' => $categoryIds,
            ]);

        $response->assertOk();
        $this->assertStringContainsString('backup-catalogo-productos-', (string) $response->headers->get('content-disposition'));
    }

    public function test_catalog_backup_prefers_official_category_banner_and_keeps_legacy_fallback(): void
    {
        $category = Category::create([
            'name' => 'Banner detector test',
            'slug' => 'banner-detector-test',
            'description' => 'Temporal',
            'active' => true,
            'sort_order' => 998,
        ]);
        $officialPath = 'assets/categories/banner-detector-test.jpg';
        $legacyPath = 'assets/gocenter/category-banner-detector-test.jpg';

        File::ensureDirectoryExists(public_path('assets/categories'));
        File::ensureDirectoryExists(public_path('assets/gocenter'));
        File::put(public_path($officialPath), 'official');
        File::put(public_path($legacyPath), 'legacy');

        $service = app(CatalogBackupService::class);
        $method = new \ReflectionMethod($service, 'categoryBanner');
        $method->setAccessible(true);

        try {
            $banner = $method->invoke($service, $category);
            $this->assertSame($officialPath, $banner['path']);

            File::delete(public_path($officialPath));

            $banner = $method->invoke($service, $category);
            $this->assertSame($legacyPath, $banner['path']);
        } finally {
            File::delete([public_path($officialPath), public_path($legacyPath)]);
        }
    }

    public function test_super_admin_can_view_catalog_backup_category_selector(): void
    {
        $superAdmin = User::where('email', 'superadmin@local.test')->firstOrFail();
        $category = \App\Models\Category::query()->firstOrFail();

        $this->actingAs($superAdmin)
            ->get(route('admin.backups.catalog.index'))
            ->assertOk()
            ->assertSee('Catalogo por categorias')
            ->assertSee('Marcar todas')
            ->assertSee($category->name);
    }

    public function test_limited_admin_cannot_download_catalog_backup(): void
    {
        $admin = User::where('email', 'admin@local.test')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.backups.catalog.download'), [
                'category_ids' => [\App\Models\Category::query()->value('id')],
            ])
            ->assertForbidden();
    }

    public function test_super_admin_can_preview_catalog_cleanup(): void
    {
        $superAdmin = User::where('email', 'superadmin@local.test')->firstOrFail();
        $category = \App\Models\Category::query()->firstOrFail();

        $this->actingAs($superAdmin)
            ->get(route('admin.catalog-cleanup.index', ['category_id' => $category->id]))
            ->assertOk()
            ->assertSee('Eliminar productos por categoria')
            ->assertSee($category->name)
            ->assertSee('Vista previa');
    }

    public function test_limited_admin_cannot_cleanup_catalog(): void
    {
        $admin = User::where('email', 'admin@local.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.catalog-cleanup.index'))
            ->assertForbidden();
    }

    public function test_super_admin_can_delete_category_products_and_keep_shared_files(): void
    {
        $superAdmin = User::where('email', 'superadmin@local.test')->firstOrFail();
        File::ensureDirectoryExists(public_path('assets/test-cleanup'));

        $uniquePath = 'assets/test-cleanup/unique-cleanup.jpg';
        $sharedPath = 'assets/test-cleanup/shared-cleanup.jpg';
        File::put(public_path($uniquePath), 'unique');
        File::put(public_path($sharedPath), 'shared');

        $category = Category::create([
            'name' => 'Categoria a borrar',
            'slug' => 'categoria-a-borrar',
            'description' => 'Temporal',
            'active' => true,
            'sort_order' => 999,
        ]);
        $otherCategory = Category::create([
            'name' => 'Categoria protegida',
            'slug' => 'categoria-protegida',
            'description' => 'Temporal',
            'active' => true,
            'sort_order' => 1000,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Producto temporal',
            'slug' => 'producto-temporal-cleanup',
            'sku' => 'SKU-TEMP-CLEANUP',
            'brand' => 'Test',
            'description' => 'Producto temporal',
            'price' => 100,
            'stock' => 5,
            'featured' => false,
            'active' => true,
        ]);
        $otherProduct = Product::create([
            'category_id' => $otherCategory->id,
            'name' => 'Producto protegido',
            'slug' => 'producto-protegido-cleanup',
            'sku' => 'SKU-PROTECTED-CLEANUP',
            'brand' => 'Test',
            'description' => 'Producto protegido',
            'price' => 100,
            'stock' => 5,
            'featured' => false,
            'active' => true,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-TEMP-CLEANUP-V1',
            'size' => 'M',
            'price_modifier' => 0,
            'stock' => 2,
            'active' => true,
        ]);
        ProductImage::create(['product_id' => $product->id, 'path' => $uniquePath, 'alt' => 'Unica', 'sort_order' => 0]);
        ProductImage::create(['product_id' => $product->id, 'path' => $sharedPath, 'alt' => 'Compartida', 'sort_order' => 1]);
        ProductImage::create(['product_id' => $otherProduct->id, 'path' => $sharedPath, 'alt' => 'Compartida', 'sort_order' => 0]);

        $this->actingAs($superAdmin)
            ->delete(route('admin.catalog-cleanup.destroy'), [
                'category_id' => $category->id,
                'confirmation' => $category->slug,
                'delete_files' => '1',
            ])
            ->assertRedirect(route('admin.catalog-cleanup.index', ['category_id' => $category->id]))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_images', ['product_id' => $product->id]);
        $this->assertDatabaseMissing('product_variants', ['product_id' => $product->id]);
        $this->assertDatabaseHas('products', ['id' => $otherProduct->id]);
        $this->assertDatabaseHas('product_images', ['product_id' => $otherProduct->id, 'path' => $sharedPath]);
        $this->assertFalse(File::exists(public_path($uniquePath)));
        $this->assertTrue(File::exists(public_path($sharedPath)));

        File::delete(public_path($sharedPath));
    }

    private function makePendingClipOrder(): Order
    {
        $order = Order::create([
            'folio' => Order::makeFolio(),
            'customer_name' => 'Juan Perez',
            'customer_email' => 'juan@example.com',
            'customer_phone' => '5512345678',
            'street' => 'Av Reforma',
            'external_number' => '123',
            'neighborhood' => 'Centro',
            'city' => 'Cuauhtemoc',
            'state' => 'CDMX',
            'postal_code' => '06000',
            'subtotal' => 1000,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 1000,
            'payment_method' => 'clip',
            'status' => Order::STATUS_PENDING_CLIP,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'method' => 'clip',
            'provider' => 'clip',
            'status' => 'pending',
            'amount' => 1000,
            'currency' => 'MXN',
            'external_reference' => $order->folio,
        ]);

        return $order;
    }
}
