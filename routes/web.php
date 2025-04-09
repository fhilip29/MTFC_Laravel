<?php

use Illuminate\Support\Facades\Route;



Route::get('/home', function () {
    return view('home');
})->name('home');

Route::get('/login', function () {
    return view('auth/login');
});

Route::get('/signup', function () {
    return view('signup');
});

Route::get('/forgot', function () {
    return view('auth/forgot_password');
});

Route::get('/about', function (){
    return view('about');
});




