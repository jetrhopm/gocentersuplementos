<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CatalogBackupController as AdminCatalogBackupController;
use App\Http\Controllers\Admin\CatalogCleanupController as AdminCatalogCleanupController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ClipWebhookController;
use App\Http\Controllers\PostalCodeController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoreController::class, 'home'])->name('home');
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');
Route::get('/productos', [StoreController::class, 'products'])->name('products.index');
Route::get('/categoria/{category}', [StoreController::class, 'category'])->name('categories.show');
Route::get('/ofertas', [StoreController::class, 'offers'])->name('offers');
Route::get('/productos/{product}', [StoreController::class, 'show'])->name('products.show');
Route::get('/consultar-pedido', [StoreController::class, 'lookup'])->name('orders.lookup');
Route::get('/consultar-pedido/buscar', [StoreController::class, 'lookupResult'])->middleware('throttle:12,1')->name('orders.lookup.result');
Route::get('/pedido/{order}/ver', [StoreController::class, 'publicOrder'])->middleware('signed')->name('orders.public.show');
Route::get('/sitemap.xml', [StoreController::class, 'sitemap'])->name('sitemap');
Route::get('/api/codigos-postales/{postalCode}', [PostalCodeController::class, 'show'])
    ->whereNumber('postalCode')
    ->middleware('throttle:60,1')
    ->name('postal-codes.show');

Route::view('/privacidad', 'store.policy', ['type' => 'privacy'])->name('policies.privacy');
Route::view('/terminos', 'store.policy', ['type' => 'terms'])->name('policies.terms');
Route::view('/devoluciones', 'store.policy', ['type' => 'returns'])->name('policies.returns');
Route::view('/envios', 'store.policy', ['type' => 'shipping'])->name('policies.shipping');

Route::prefix('carrito')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/', [CartController::class, 'store'])->middleware('throttle:90,1')->name('store');
    Route::post('/cupon', [CartController::class, 'applyCoupon'])->middleware('throttle:30,1')->name('coupon.apply');
    Route::delete('/cupon', [CartController::class, 'removeCoupon'])->middleware('throttle:30,1')->name('coupon.remove');
    Route::delete('/vaciar', [CartController::class, 'clear'])->middleware('throttle:20,1')->name('clear');
    Route::patch('/{key}', [CartController::class, 'update'])->middleware('throttle:90,1')->name('update');
    Route::delete('/{key}', [CartController::class, 'destroy'])->middleware('throttle:90,1')->name('destroy');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'show'])->name('show');
    Route::post('/', [CheckoutController::class, 'store'])->middleware('throttle:6,1')->name('store');
    Route::get('/pedido-recibido/{order}', [CheckoutController::class, 'received'])->middleware('signed')->name('received');
    Route::post('/pedido-recibido/{order}/referencia', [CheckoutController::class, 'transferReference'])->middleware(['signed', 'throttle:12,1'])->name('transfer.reference');
});

Route::post('/pago/reintentar/{order}', [CheckoutController::class, 'pay'])->middleware(['signed', 'throttle:12,1'])->name('checkout.pay');
Route::get('/pago/clip/exito', [CheckoutController::class, 'clipSuccess'])->name('checkout.clip.success');
Route::get('/pago/clip/error', [CheckoutController::class, 'clipError'])->name('checkout.clip.error');
Route::get('/pago/clip/retorno/{order}', [CheckoutController::class, 'clipReturn'])->middleware(['signed', 'throttle:30,1'])->name('checkout.clip.return');
Route::get('/pago/clip/cancelado/{order}', [CheckoutController::class, 'clipCancelled'])->middleware(['signed', 'throttle:30,1'])->name('checkout.clip.cancelled');
Route::get('/pago/clip/webhook', ClipWebhookController::class)->name('webhooks.clip.legacy.status');
Route::post('/pago/clip/webhook', ClipWebhookController::class)->middleware('throttle:120,1')->name('webhooks.clip.legacy');
Route::get('/webhooks/clip', ClipWebhookController::class)->name('webhooks.clip.status');
Route::post('/webhooks/clip', ClipWebhookController::class)->middleware('throttle:120,1')->name('webhooks.clip');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/login', [AuthController::class, 'authenticate'])->middleware('throttle:5,1')->name('authenticate');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/pedidos/exportar', [AdminOrderController::class, 'export'])->name('orders.export');
        Route::get('/configuracion', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::post('/configuracion/clip/probar', [AdminSettingsController::class, 'testClip'])->name('settings.clip.test');
        Route::post('/configuracion/correo/probar', [AdminSettingsController::class, 'testMail'])->name('settings.mail.test');
        Route::post('/configuracion/meta/probar', [AdminSettingsController::class, 'testMeta'])->name('settings.meta.test');
        Route::put('/configuracion', [AdminSettingsController::class, 'update'])->name('settings.update');
        Route::resource('productos', AdminProductController::class)->names('products')->parameters(['productos' => 'product'])->except('show');
        Route::delete('/productos/{product}/imagenes/{image}', [AdminProductController::class, 'destroyImage'])->name('products.images.destroy');
        Route::resource('categorias', AdminCategoryController::class)->names('categories')->parameters(['categorias' => 'category'])->except('show');
        Route::resource('cupones', AdminCouponController::class)->names('coupons')->parameters(['cupones' => 'coupon'])->except('show');
        Route::resource('administradores', AdminUserController::class)->names('users')->parameters(['administradores' => 'user'])->except('show');
        Route::get('/backups/catalogo', [AdminCatalogBackupController::class, 'index'])->name('backups.catalog.index');
        Route::post('/backups/catalogo/descargar', [AdminCatalogBackupController::class, 'download'])->name('backups.catalog.download');
        Route::get('/catalogo/limpieza', [AdminCatalogCleanupController::class, 'index'])->name('catalog-cleanup.index');
        Route::delete('/catalogo/limpieza', [AdminCatalogCleanupController::class, 'destroy'])->name('catalog-cleanup.destroy');
        Route::get('/pedidos/{order}/imprimir', [AdminOrderController::class, 'print'])->name('orders.print');
        Route::post('/pedidos/{order}/recordatorio-pago', [AdminOrderController::class, 'sendPaymentReminder'])->name('orders.payment-reminder');
        Route::patch('/pedidos/{order}/estado', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        Route::resource('pedidos', AdminOrderController::class)->names('orders')->parameters(['pedidos' => 'order'])->only(['index', 'show', 'destroy']);
    });
});
