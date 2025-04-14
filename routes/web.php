<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// rota teste
Route::get('/', function () {
    return Inertia::render('Home');
});
