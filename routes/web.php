<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// rota teste
Route::get('/', function () {
    return Inertia::render('Home');
});

/**
 * @description Rota para view de cadastro de usuário
 */

Route::get('/register', function () {
    return Inertia::render('Register');
});
