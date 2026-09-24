<?php

use Illuminate\Support\Facades\Route;

// Host SPA Blade view for root route
Route::get('/', function () {
    return view('welcome');
});

// Fallback for all other web SPA routes (Vue Router will handle rendering)
Route::fallback(function () {
    return view('welcome');
});
