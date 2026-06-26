<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
