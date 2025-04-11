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

Route::get('/tailwind-test', function () {
    return view('tailwind-test');
});

// 👇 Add these dummy routes so your header/footer doesn't crash
Route::view('/about', 'about')->name('about');
Route::view('/classes', 'classes')->name('classes');
Route::view('/trainer', 'trainer')->name('trainer');
Route::view('/pricing', 'pricing')->name('pricing');
Route::view('/notifications', 'notifications')->name('notifications');
Route::view('/terms', 'terms')->name('terms');
Route::view('/privacypolicy', 'privacy')->name('privacy');

// Dummy user/profile/admin routes (for dropdown, no errors for now)
Route::view('/profile/settings', 'profile-settings')->name('profile.settings');
Route::view('/profile', 'profile')->name('profile');
Route::view('/trainer/profile', 'trainer-profile')->name('trainer.profile');
Route::view('/admin/dashboard', 'admin-dashboard')->name('admin.dashboard');
Route::view('/community_dashboard', 'community')->name('community');
Route::view('/orders', 'orders')->name('orders');

// Log out fallback (no actual logic yet)
Route::post('/logout', function () {
    return redirect()->route('home');
})->name('logout');




