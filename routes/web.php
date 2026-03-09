<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/w', function () {
    return "<h1>Welcome to my website</h1>";
});
