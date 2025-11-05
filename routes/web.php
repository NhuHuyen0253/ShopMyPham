<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BodyController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\HairController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MakeupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\HotDealController;
use App\Http\Controllers\BrandController;



Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
//Route::resource('/product', ProductController::class);
Route::get('/products/category/{id}', [ProductController::class, 'filterByCategory'])->name('products.category');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/account/info', [UserController::class, 'info'])->name('account.info');


Route::get('/store', [StoreController::class, 'index'])->name('store');


Route::get('/warranty', [WarrantyController::class, 'form'])->name('warranty.form');
Route::post('/warranty', [WarrantyController::class, 'submit'])->name('warranty');

Route::get('/contact', [ContactController::class, 'index'])->name('support');


Route::get('/hot-deal', [HotDealController::class, 'index'])->name('hotdeal');;

Route::get('/brands', [BrandController::class, 'index'])->name('brands');
Route::get('/brands/{slug}', [BrandController::class, 'show'])->name('brands.show');

Route::get('/face', [FaceController::class, 'index'])->name('face');
Route::get('/face/{slug}', [FaceController::class, 'category'])->name('face.category');

Route::get('/products/category/{id}', [ProductController::class, 'filterByCategory'])->name('products.category');

Route::get('/hair', [HairController::class, 'index'])->name('hair');
Route::get('/hair/{slug}', [HairController::class, 'category'])->name('hair.category');

Route::get('/body', [BodyController::class, 'index'])->name('body');
Route::get('/body/{slug}', [BodyController::class, 'category'])->name('body.category');

Route::get('/makeup', [MakeupController::class, 'index'])->name('makeup');
Route::get('/makeup/{slug}', [MakeupController::class, 'category'])->name('makeup.category');

Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
Route::post('/cart/update-qty', [CartController::class, 'updateQty'])->name('cart.updateQty');



Route::middleware('auth')->group(function () {
    Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('cart.add') ;
    Route::get('cart', [CartController::class, 'viewCart'])->name('cart.view');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    Route::post('/update/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::post('remove', [CartController::class, 'remove'])->name('cart.remove');
});

Route::middleware('auth')->group(function () {
    Route::get('checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('checkout', [CheckoutController::class, 'process'])->name('checkout.process');
});


Route::post('/order/buynow',   [OrderController::class, 'buynow'])->name('order.buynow');
Route::post('/order/confirm',  [OrderController::class, 'confirm'])->name('order.confirm');
Route::post('/order/checkout', [OrderController::class, 'checkout'])->name('order.checkout');
Route::get ('/order/{id}',     [OrderController::class, 'show'])->name('order.show');



Route::middleware('auth')->group(function () {
    Route::get('/account-info', [AccountController::class, 'show'])->name('account.info');
    Route::post('/update-account', [AccountController::class, 'update'])->name('updateAccount');
});





// Route admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login');
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
    
    Route::get('/register', [\App\Http\Controllers\Admin\AuthController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [\App\Http\Controllers\Admin\AuthController::class, 'register'])->name('register');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/password/change', [\App\Http\Controllers\Admin\AdminController::class, 'showChangePasswordForm'])->name('password.change');
        Route::post('/password/change', [\App\Http\Controllers\Admin\AdminController::class, 'changePassword'])->name('password.update');
        
        
        Route::resource('/product', \App\Http\Controllers\Admin\ProductController::class);
        
        Route::resource('/orders', \App\Http\Controllers\Admin\OrderController::class);
        Route::resource('/users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('/brand', \App\Http\Controllers\Admin\BrandController::class);
        Route::put('/product/{product}', [ProductController::class, 'update'])->name('product.update');


        
    });
});


