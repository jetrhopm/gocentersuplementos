<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
