<?php

use App\Http\Controllers\Cart\AddCartItemController;
use App\Http\Controllers\Cart\CartDetailController;
use App\Http\Controllers\Cart\CheckoutController;
use App\Http\Controllers\Cart\RemoveCartItemController;
use App\Http\Controllers\Cart\RemoveCouponController;
use App\Http\Controllers\Cart\UseCouponController;
use App\Http\Controllers\Orders\OrderDetailController;
use App\Http\Controllers\Orders\OrderIndexController;
use App\Http\Controllers\Orders\OrderPdfController;
use App\Http\Controllers\Orders\OrderPdfPreviewController;
use App\Http\Controllers\Payment\FailController;
use App\Http\Controllers\Payment\PayController;
use App\Http\Controllers\Products\FiltersController;
use App\Http\Controllers\Products\ProductIndexController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', ProductIndexController::class);

Route::prefix('cart')->group(function () {
    Route::get('/', CartDetailController::class);
    Route::get('/add/{product}', AddCartItemController::class);
    Route::get('/remove/{cartItem}', RemoveCartItemController::class);

    Route::post('/checkout', CheckoutController::class);

    Route::post('/coupon/use', UseCouponController::class);
    Route::get('/coupon/remove', RemoveCouponController::class);
});

Route::prefix('orders')->group(function () {
    Route::get('/', OrderIndexController::class);
    Route::get('/{order}', OrderDetailController::class);
    Route::get('/{order}/pdf', OrderPdfController::class);
    Route::get('/{order}/pdf/preview', OrderPdfPreviewController::class);
});

Route::prefix('payments')->group(function () {
    Route::get('/pay/{payment}', PayController::class);
    Route::get('/fail/{payment}', FailController::class);
});

Route::get('product/filters', FiltersController::class);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
