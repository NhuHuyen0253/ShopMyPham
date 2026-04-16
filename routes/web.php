<?php

use App\Http\Controllers\Admin\ProductManagementController;
use App\Http\Controllers\Admin\PromoBannerController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\ChatboxController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;        // Frontend order
use App\Http\Controllers\BodyController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\HairController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MakeupController;
use App\Http\Controllers\ProductController;      // Frontend product
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\HotDealController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\VnPayController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin\AdminContactController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\StockLevelController;


/*
|--------------------------------------------------------------------------
| Front site
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/products/category/{id}', [ProductController::class, 'filterByCategory'])->name('products.category');

Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('product.reviews.store');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');


Route::get('/profile/info', [ProfileController::class, 'show'])->name('profile.info');
Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');

Route::get('/forgotpassword', [AuthController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgotpassword', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/resetpassword/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/resetpassword', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/store', [StoreController::class, 'index'])->name('store');

Route::get('/warranty', [WarrantyController::class, 'form'])->name('warranty.form');
Route::post('/warranty', [WarrantyController::class, 'submit'])->name('warranty');

Route::get('/contact', [ContactController::class, 'index'])->name('contact.contact');
Route::get('/contact/return-policy', fn () => view('contact.return-policy'));
Route::get('/contact/privacy-policy', fn () => view('contact.privacy-policy'));
Route::get('/contact/payment-policy', fn () => view('contact.payment-policy'));
Route::get('/contact/purchase-instructions', fn () => view('contact.purchase-instructions'));
Route::get('/contact/ship', fn () => view('contact.ship'));
Route::get('/contact/introduce', fn () => view('contact.introduce'));
Route::get('/contact/recruitment', fn () => view('contact.recruitment'));

Route::get('/hot-deal', [HotDealController::class, 'index'])->name('hotdeal');


Route::get('/brands', [BrandController::class, 'index'])->name('brands');
Route::get('/brands/{slug}', [BrandController::class, 'show'])->name('brands.show');

Route::get('/face', [FaceController::class, 'index'])->name('face');
Route::get('/face/{slug}', [FaceController::class, 'category'])->name('face.category');

Route::get('/hair', [HairController::class, 'index'])->name('hair');
Route::get('/hair/{slug}', [HairController::class, 'category'])->name('hair.category');

Route::get('/body', [BodyController::class, 'index'])->name('body');
Route::get('/body/{slug}', [BodyController::class, 'category'])->name('body.category');

Route::get('/makeup', [MakeupController::class, 'index'])->name('makeup');
Route::get('/makeup/{slug}', [MakeupController::class, 'category'])->name('makeup.category');

/* Cart (ai cũng cần xem số lượng) */
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
Route::post('/cart/{id}/update-qty', [CartController::class, 'updateQty'])->name('cart.updateQty');
Route::post('/cart/apply-promotion', [CartController::class, 'applyPromotion'])->name('cart.apply-promotion');
Route::post('/cart/remove-promotion', [CartController::class, 'removePromotion'])->name('cart.remove-promotion');
Route::post('/cart/checkout', [OrderController::class, 'checkoutFromCart'])->name('cart.checkoutFromCart');
Route::post('/cart/{id}/remove', [CartController::class, 'remove'])->name('cart.remove');

/*Chatbox*/
Route::post('/chatbox/store', [ChatboxController::class, 'store'])->name('chatbox.store');
Route::get('/chatbox/messages', [ChatboxController::class, 'getMessages'])->name('chatbox.messages');

Route::middleware('auth')->group(function () {
    Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.view');
    Route::post('/update/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
});

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
});

/* Frontend order (khách) */
Route::post('/order/buynow', [OrderController::class, 'buynow'])->name('order.buynow');
Route::get('/order/confirm', [OrderController::class, 'confirm'])->name('order.confirm');
Route::post('/order/checkout', [OrderController::class, 'checkout'])->name('order.checkout');
Route::get('/order/{id}', [OrderController::class, 'showorder'])->name('order.showorder');
Route::post('/order/{id}/update-quantity', [OrderController::class, 'updateQuantity'])->name('order.updateQty');
Route::get('/order/{id}/notice', [OrderController::class, 'notice'])->name('order.notice');
Route::post('/order/{id}/apply-promotion',  [OrderController::class, 'applyPromotion'])->name('order.applyPromotion');
Route::post('/order/{id}/remove-promotion', [OrderController::class, 'removePromotion'])->name('order.removePromotion');
Route::patch('/order/{id}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');

/*Wishlist*/

Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

/*VNPay*/

Route::get('/vnpay/create/{order}', [VnPayController::class, 'create'])->name('vnpay.create');
Route::get('/vnpay-return', [VnPayController::class, 'return'])->name('vnpay.return');
Route::match(['GET', 'POST'], '/vnpay-ipn', [VnPayController::class, 'ipn'])->name('vnpay.ipn');

/*Q&A*/ 
Route::middleware('auth')->group(function () {
    Route::get('/profile/rebuy', [ProfileController::class, 'rebuy'])->name('profile.rebuy');
    Route::get('/profile/faq', [ProfileController::class, 'faq'])->name('profile.faq');
    Route::post('/profile/faq', [ProfileController::class, 'sendQuestion'])->name('profile.faq.send');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Auth (admin)
    Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login');
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

    Route::get('/register', [\App\Http\Controllers\Admin\AuthController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [\App\Http\Controllers\Admin\AuthController::class, 'register'])->name('register');

    Route::get('/forgot-password', [\App\Http\Controllers\Admin\AuthController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Admin\AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Admin\AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Admin\AuthController::class, 'resetPassword'])->name('password.update');

    Route::middleware('auth:admin')->group(function () {

        Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/change-password', [AdminProfileController::class, 'editPassword'])->name('profile.password.edit');
        Route::post('/profile/change-password', [AdminProfileController::class, 'updatePassword'])->name('profile.password.update');
        
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/password/change', [\App\Http\Controllers\Admin\AdminController::class, 'showChangePasswordForm'])->name('password.change');
        Route::post('/password/change', [\App\Http\Controllers\Admin\AdminController::class, 'changePassword'])->name('password.change.submit');
        Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('product.reviews.store');

        /* Product (admin) */
        Route::resource('/product', \App\Http\Controllers\Admin\ProductController::class);
        Route::delete('/product/images/{image}',[\App\Http\Controllers\Admin\ProductController::class, 'destroyImage'])->name('product.images.destroy');

        /*product_management*/
        Route::get('product-management',        [ProductManagementController::class, 'index'])->name('product_management.index');
        Route::get('product-management/{product}/edit', [ProductManagementController::class, 'edit'])->name('product_management.edit');
        Route::put('product-management/{product}',      [ProductManagementController::class, 'update'])->name('product_management.update');

        /* Orders (admin) */
        Route::resource('/orders', \App\Http\Controllers\Admin\OrderController::class);
        Route::patch('/orders/{id}/paid',[\App\Http\Controllers\Admin\OrderController::class, 'togglePaid'])->name('orders.paid'); // duy nhất 1 route paid
        Route::post('/orders/bulk',[\App\Http\Controllers\Admin\OrderController::class, 'bulk'])->name('orders.bulk');
        Route::patch('/orders/{id}/refund', [\App\Http\Controllers\Admin\OrderController::class, 'toggleRefund'])->name('orders.refund');
        Route::post('/orders/{id}/confirm', [\App\Http\Controllers\Admin\OrderController::class, 'confirm'])->name('orders.confirm');
        Route::post('/orders/{id}/shipping', [\App\Http\Controllers\Admin\OrderController::class, 'updateShipping'])->name('orders.shipping.update');
        Route::post('/orders/{id}/cancel', [\App\Http\Controllers\Admin\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::patch('/orders/{id}/toggle-paid', [App\Http\Controllers\Admin\OrderController::class, 'togglePaid'])->name('orders.togglePaid');

        /* Users / Brand */
        Route::resource('/users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('/brand', \App\Http\Controllers\Admin\BrandController::class);

        /* Customers */
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{user}', [CustomerController::class, 'show'])->name('customers.show');
        Route::patch('/customers/{user}/toggle', [CustomerController::class, 'toggleStatus'])->name('customers.toggle');
        Route::delete('/customers/{user}', [CustomerController::class, 'destroy'])->name('customers.destroy');

        /* Employee */
        Route::resource('employee', EmployeeController::class);
        
        /*Supplier*/
        Route::get('supplier', [SupplierController::class, 'index'])->name('supplier.index');
        Route::get('supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
        Route::post('supplier', [SupplierController::class, 'store'])->name('supplier.store');
        Route::get('supplier/{supplier}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
        Route::put('supplier/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');
        Route::delete('supplier/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
        
        /*stock*/
        Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
        Route::post('/stock/in', [StockController::class, 'stockIn'])->name('stock.in');
        Route::post('/stock/out', [StockController::class, 'stockOut'])->name('stock.out');
        Route::patch('/stock/{id}/sku', [StockController::class, 'updateSku'])->name('stock.updateSku');
        Route::get('/stock/history', [StockController::class, 'history'])->name('stock.history');

        Route::get('/stock-levels', [StockLevelController::class, 'index'])->name('stock_levels.index');
        Route::get('/stock-levels/create', [StockLevelController::class, 'create'])->name('stock_levels.create');
        Route::post('/stock-levels', [StockLevelController::class, 'store'])->name('stock_levels.store');
        Route::get('/stock-levels/{stockLevel}/edit', [StockLevelController::class, 'edit'])->name('stock_levels.edit');
        Route::put('/stock-levels/{stockLevel}', [StockLevelController::class, 'update'])->name('stock_levels.update');
        Route::delete('/stock-levels/{stockLevel}', [StockLevelController::class, 'destroy'])->name('stock_levels.destroy');

        
        Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class)->except(['show']);
        Route::resource('warehouses', \App\Http\Controllers\Admin\WarehouseController::class)->except(['show']);

        Route::resource('promotions', PromotionController::class);
        Route::resource('promo-banners', PromoBannerController::class)->except(['show']);

         Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');

        Route::post('reviews/{review}/reply', [AdminReviewController::class, 'reply'])->name('reviews.reply');

        Route::post('reviews/{review}/status', [AdminReviewController::class, 'changeStatus'])->name('reviews.status');

        /*Revenue*/
        Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue.index');

        /*Q&A*/ 
        Route::get('/contacts', [AdminContactController::class, 'index'])->name('contacts.index');
        Route::get('/contacts/{contact}', [AdminContactController::class, 'show'])->name('contacts.show');
        Route::post('/contacts/{contact}/reply', [AdminContactController::class, 'reply'])->name('contacts.reply');
        Route::patch('/contacts/{contact}/status', [AdminContactController::class, 'updateStatus'])->name('contacts.status');
        Route::delete('/contacts/{contact}', [AdminContactController::class, 'destroy'])->name('contacts.destroy'); 

        /*category*/
        Route::resource('category', CategoryController::class);

      
       
    });
});
