<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminMemberController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\EquipmentMaintenanceController;
use App\Http\Controllers\VendorController;
use App\Http\Middleware\RoleMiddleware;


//Role Middleware
// Only Admins
//Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.admin_dashboard');
    })->name('admin.dashboard');
//});

// Only Trainers
Route::middleware(['auth', 'role:trainer'])->group(function () {
    Route::get('/trainer/dashboard', function () {
        return view('trainer.dashboard');
    })->name('trainer.dashboard');
});

// Only Members
//Route::middleware(['auth', 'role:member'])->group(function () {
    Route::get('/', function () {
        return view('home');
    })->name('member.dashboard');
//});




Route::get('/', function () {
    return view('home');
})->name('home');


// ===================
// AUTH ROUTES
// ===================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup.form');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Fallback for logout if POST request fails
Route::get('/logout', [AuthController::class, 'logout']);

Route::view('/forgot', 'auth.forgot_password')->name('forgot_password');


// ===================
// HEADER BTNS ROUTES
// ===================
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::view('/trainers', 'trainers')->name('trainers');

// ===================
// PRICING ROUTES
// ===================
Route::view('/pricing', 'pricing')->name('pricing');
Route::view('/pricing/gym', 'pricing.gym')->name('pricing.gym');
Route::view('/pricing/boxing', 'pricing.boxing')->name('pricing.boxing');
Route::view('/pricing/muay', 'pricing.muay')->name('pricing.muay');
Route::view('/pricing/jiu', 'pricing.jiu')->name('pricing.jiu');
// ===================
// TERMS / POLICIES
// ===================
Route::view('/terms', 'terms')->name('terms');
Route::view('/privacypolicy', 'privacy')->name('privacy');

// ===================
// ALL USER ROUTES
// ===================

Route::view('/notifications', 'notifications')->name('notifications');
Route::view('/account-settings', 'account-settings')->name('account-settings');

// Order routes (protected by auth middleware)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');


Route::view('/community', 'community')->name('community');
Route::view('/payment-method', 'payment-method')->name('payment-method');
Route::view('/profile/settings', 'profile-settings')->name('profile.settings');


// ===================
// USER ROUTES
// ===================
Route::view('/profile', 'profile')->name('profile');



// ===================
// TRAINER ROUTES
// ===================
Route::middleware(['auth', 'role:trainer'])->group(function () {
Route::view('/trainer/profile', 'trainer-profile')->name('trainer.profile');
});


// ===================
// ADMIN ROUTES
// ===================


Route::view('/admin/trainer/admin_trainer', 'admin.trainer.admin_trainer')->name('admin.trainer.admin_trainer');

//Members module
Route::get('/admin/members/admin_members', [AdminMemberController::class, 'index'])->name('admin.members.admin_members');
Route::get('/admin/members/{id}', [AdminMemberController::class, 'show'])->name('admin.members.show');
Route::get('/admin/members/{user}/subscriptions', [AdminMemberController::class, 'manageMemberSubscriptions'])->name('admin.members.subscriptions');
Route::post('/admin/members/{user}/subscriptions', [AdminMemberController::class, 'storeSubscription'])->name('admin.members.subscriptions.store');
Route::put('/admin/members/{user}/subscriptions/{subscription}', [AdminMemberController::class, 'updateSubscription'])->name('admin.members.subscriptions.update');
Route::post('/admin/members/{user}/subscriptions/{subscription}/cancel', [AdminMemberController::class, 'cancelSubscription'])->name('admin.members.subscriptions.cancel');
Route::post('/admin/members/{user}/archive', [AdminMemberController::class, 'archiveMember'])->name('admin.members.archive');

//Sessions module
Route::get('/admin/session/admin_session', [SessionController::class, 'index'])->name('admin.session.admin_session');
Route::post('/admin/sessions/store', [SessionController::class, 'store'])->name('admin.session.store');


//Products module
Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.product.products');
Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.product.store');
Route::put('/admin/products/{id}', [ProductController::class, 'update'])->name('admin.product.update');
Route::delete('/admin/products/{id}', [ProductController::class, 'destroy'])->name('admin.product.destroy');




Route::view('/admin/invoice', 'admin.invoice.admin_invoice')->name('admin.invoice.invoice');
Route::view('/admin/promotion', 'admin.promotion.admin_promo')->name('admin.promotion.promo');
Route::view('/admin/equipment', 'admin.gym.admin_gym')->name('admin.gym.gym');



// Admin Orders Management
Route::get('/admin/orders', [AdminOrderController::class, 'index'])->name('admin.orders.orders');
Route::get('/admin/orders/{id}/details', [AdminOrderController::class, 'showDetails'])->name('admin.orders.details');
Route::post('/admin/orders/{id}/update-status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.update-status');



// ===================
// CART ROUTES
// ===================
Route::get('/cart', [CartController::class, 'showCart'])->name('cart');
Route::get('/cart/get', [CartController::class, 'getCart'])->name('cart.get')->middleware('auth');
Route::post('/cart/sync', [CartController::class, 'syncCart'])->name('cart.sync')->middleware('auth');
Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');


// Equipment management routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
    // Equipment routes
    Route::get('/equipment', [EquipmentController::class, 'index'])->name('admin.gym.gym');
    Route::get('/equipment/{equipment}', [EquipmentController::class, 'show'])->name('admin.gym.equipment.show');
    Route::post('/equipment', [EquipmentController::class, 'store'])->name('admin.gym.equipment.store');
    Route::put('/equipment/{equipment}', [EquipmentController::class, 'update'])->name('admin.gym.equipment.update');
    Route::delete('/equipment/{equipment}', [EquipmentController::class, 'destroy'])->name('admin.gym.equipment.destroy');
    
    // Maintenance routes
    Route::get('/equipment/maintenance/logs', [EquipmentMaintenanceController::class, 'index'])->name('admin.gym.maintenance');
    Route::post('/equipment/maintenance', [EquipmentMaintenanceController::class, 'store'])->name('admin.gym.maintenance.store');
    Route::delete('/equipment/maintenance/{maintenance}', [EquipmentMaintenanceController::class, 'destroy'])->name('admin.gym.maintenance.destroy');
    
    // Vendor routes
    Route::get('/vendors', [VendorController::class, 'index'])->name('admin.gym.vendors');
    Route::post('/vendors', [VendorController::class, 'store'])->name('admin.gym.vendors.store');
    Route::get('/vendors/{vendor}', [VendorController::class, 'show'])->name('admin.gym.vendors.show');
    Route::put('/vendors/{vendor}', [VendorController::class, 'update'])->name('admin.gym.vendors.update');
    Route::delete('/vendors/{vendor}', [VendorController::class, 'destroy'])->name('admin.gym.vendors.destroy');
});


