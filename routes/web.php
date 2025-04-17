<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


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

Route::view('/forgot', 'auth.forgot_password')->name('forgot_password');


// ===================
// HEADER BTNS ROUTES
// ===================
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::view('/shop', 'shop')->name('shop');
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
Route::middleware(['auth', 'role:user,trainer,admin'])->group(function () {
Route::view('/notifications', 'notifications')->name('notifications');
Route::view('/account-settings', 'account-settings')->name('account-settings');
Route::view('/orders', 'orders')->name('orders');
Route::view('/community', 'community')->name('community');
Route::view('/payment-method', 'payment-method')->name('payment-method');
Route::view('/checkout', 'checkout')->name('checkout');
Route::view('/cart', 'cart')->name('cart');
Route::view('/profile/settings', 'profile-settings')->name('profile.settings');
});

// ===================
// USER ROUTES
// ===================
Route::middleware(['auth', 'role:user'])->group(function () {
Route::view('/profile', 'profile')->name('profile');
});

// ===================
// TRAINER ROUTES
// ===================
Route::middleware(['auth', 'role:trainer'])->group(function () {
Route::view('/trainer/profile', 'trainer-profile')->name('trainer.profile');
});

// ===================
// ADMIN ROUTES
// ===================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
Route::view('/admin', 'admin.admin_dashboard')->name('admin');
Route::view('/admin/dashboard', 'admin.admin_dashboard')->name('admin.dashboard');
Route::view('/admin/trainer/admin_trainer', 'admin.trainer.admin_trainer')->name('admin.trainer.admin_trainer');
Route::view('/admin/members/admin_members', 'admin.members.admin_members')->name('admin.trainers');
Route::view('/admin/invoice', 'admin.invoice.admin_invoice')->name('admin.invoice.invoice');
Route::view('/admin/session', 'admin.session.admin_session')->name('admin.session.admin_session');
Route::view('/admin/promotion', 'admin.promotion.admin_promo')->name('admin.promotion.promo');
Route::view('/admin/equipment', 'admin.gym.admin_gym')->name('admin.gym.gym');
Route::view('/admin/products', 'admin.product.admin_product')->name('admin.product.products');
Route::view('/admin/orders', 'admin.orders.admin_orders')->name('admin.orders.orders');

// Admin Manage Orders (dummy data for now)
Route::get('/admin/manage-orders', function () {
    $orders = [
        (object)['id' => '67fb3540086200c3004a8000', 'date' => '2025-04-13', 'status' => 'Accepted'],
        (object)['id' => '67fa56af95f616afe1db8f09', 'date' => '2025-04-12', 'status' => 'Pending'],
        (object)['id' => '67f1e208ccc16bbe38bce1cb', 'date' => '2025-04-06', 'status' => 'Cancelled'],
        (object)['id' => '67f0ec29da9e8fa68e4f90f8', 'date' => '2025-04-05', 'status' => 'Completed'],
        (object)['id' => '67ee504632235b11cff6e61b4', 'date' => '2025-04-03', 'status' => 'Accepted'],
        (object)['id' => '67ee31d5d8d6da4022bba2c5', 'date' => '2025-04-03', 'status' => 'Out for Delivery'],
        (object)['id' => '67eb368a40ad2ac6182106dd', 'date' => '2025-04-01', 'status' => 'Pending'],
        (object)['id' => '67ea3dd2f7296cdb6f64d684', 'date' => '2025-03-31', 'status' => 'Pending'],
        (object)['id' => '67e8d889a8e861a0117d15ba', 'date' => '2025-03-30', 'status' => 'Cancelled'],
        (object)['id' => '67e03af6d6ac4ca715075010', 'date' => '2025-03-24', 'status' => 'Pending'],
    ];

    return view('admin.orders.index', compact('orders'));
});

});
