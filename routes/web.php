<?php

use App\Http\Controllers\Auth\AuthController;
use App\Livewire\CartPage;
use App\Livewire\Catalog;
use App\Livewire\CheckoutPage;
use App\Livewire\OrderDetail;
use App\Livewire\OrderHistory;
use App\Livewire\ProductShow;
use App\Livewire\Admin\CrudPage;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\OrderManager;
use App\Livewire\Admin\ShippingPage;
use Illuminate\Support\Facades\Route;

Route::get('/', Catalog::class)->name('catalog');
Route::get('/products/{product}', ProductShow::class)->name('products.show');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store']);
});
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/cart', CartPage::class)->name('cart');
    Route::get('/checkout', CheckoutPage::class)->name('checkout');
    Route::get('/orders', OrderHistory::class)->name('orders.index');
    Route::get('/orders/{order}', OrderDetail::class)->name('orders.show');
});
Route::prefix('admin')->name('admin.')->middleware(['auth','admin'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/master/{resource}', CrudPage::class)->whereIn('resource',['kelas','vendors','categories','products','banners'])->name('crud');
    Route::get('/shipping', ShippingPage::class)->name('shipping');
    Route::get('/orders', OrderManager::class)->name('orders.index');
    Route::get('/orders/{order}', OrderManager::class)->name('orders.show');
});
