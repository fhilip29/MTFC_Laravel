<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', function () {
    return view('auth/login');
})->name('login');

Route::get('/signup', function () {
    return view('signup');
})->name('signup');

Route::get('/forgot', function () {
    return view('auth/forgot_password');
})->name('forgot_password');

Route::get('/about', function () {
    return view('about');
})->name('about');


Route::get('/contact', function () {
    return view('contact');
});

Route::get('/shop', function () {
    return view('shop');
})->name('shop');

Route::get('/notifications', function () {
    return view('notifications');
})->name('notifications');

Route::get('/trainers', function () {
    return view('trainers');
})->name('trainers');

Route::get('/account-settings', function () {
    return view('account-settings');
})->name('account-settings');

Route::get('/orders', function () {
    return view('orders');
})->name('orders');

Route::get('/profile', function () {
    return view('profile');
})->name('profile');

Route::get('/community', function () {
    return view('community');
})->name('community');

Route::get('/payment-method', function () {
    return view('payment-method');
})->name('payment-method');

//Admin routes
Route::get('/admin', function () {
    return view('layouts.admin');
})->name('admin');

Route::get('/admin/dashboard', function () {
    return view('admin.admin_dashboard');
})->name('admin.dashboard');

Route::get('/admin/trainers', function () {
    return view('admin.trainers.admin_trainers');
})->name('admin.trainers');

Route::get('/admin/members', function () {
    return view('admin.members.admin_members');
})->name('admin.trainers');

Route::get('/admin/invoices', function () {
    return view('admin.invoice.admin_invoice');
})->name('admin.invoice.invoice');

Route::get('/admin/sessions', function () {
    return view('admin.session.admin_session');
})->name('admin.session.session');

Route::get('/admin/promotions', function () {
    return view('admin.promotion.admin_promo');
})->name('admin.promotion.promo');

Route::get('/admin/equipment', function () {
    return view('admin.gym.admin_gym');
})->name('admin.gym.gym');

Route::get('/admin/products', function () {
    return view('admin.product.admin_product');
})->name('admin.product.products');


Route::get('/admin/orders', function () {
    return view('admin.orders.admin_orders');
})->name('admin.orders.orders');

//Until you connect to a DB, you can pass dummy data in your route like this:
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







//pricing routes
Route::view('/pricing/gym', 'pricing.gym')->name('pricing.gym');
Route::view('/pricing/boxing', 'pricing.boxing')->name('pricing.boxing');
Route::view('/pricing/muay', 'pricing.muay')->name('pricing.muay');
Route::view('/pricing/jiu', 'pricing.jiu')->name('pricing.jiu');

// Terms page route
Route::view('/terms', 'terms')->name('terms');

// 👇 Add these dummy routes so your header/footer doesn't crash
Route::view('/about', 'about')->name('about');

Route::view('/trainers', 'trainers')->name('trainers');
Route::view('/shop', 'shop')->name('shop');
Route::view('/contact', 'contact')->name('contact');
Route::view('/cart', 'cart')->name('cart');
Route::view('/checkout', 'checkout')->name('checkout');
Route::view('/pricing', 'pricing')->name('pricing');
Route::view('/notifications', 'notifications')->name('notifications');
Route::view('/terms', 'terms')->name('terms');
Route::view('/privacypolicy', 'privacy')->name('privacy');

// Dummy user/profile/admin routes (for dropdown, no errors for now)
Route::view('/profile/settings', 'profile-settings')->name('profile.settings');
Route::view('/profile', 'profile')->name('profile');
Route::view('/trainer/profile', 'trainer-profile')->name('trainer.profile');

Route::view('/community_dashboard', 'community')->name('community');
Route::view('/orders', 'orders')->name('orders');

// Log out fallback (no actual logic yet)
Route::post('/logout', function () {
    return redirect()->route('home');
})->name('logout');




