<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Auth\LoginController;

// rota teste
Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/clientes', function () {
    return Inertia::render('Clientes');
});

Route::get('/financeiro', function () {
    return Inertia::render('Financeiro');
});

Route::get('/cobrancas', function () {
    return Inertia::render('Cobrancas');
});

Route::get('/boletos', function () {
    return Inertia::render('Boletos');
});


/**
 * @description Rota para view de cadastro de usuário
 */

Route::get('/register', function () {
    return Inertia::render('Register');
});

Route::get('/login', function () {
    return Inertia::render('Login');
});


Route::get('/perfil-empresa', function () {
    return Inertia::render('PerfilEmpresa');
});

Route::get('/perfil-empresa', function () {
    return Inertia::render('PerfilEmpresa'); // componente Vue
})->name('perfil.empresa');

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');



// Rotas post
Route::post('/register', [RegisterController::class, 'store']);

Route::post('/login', [LoginController::class, 'login']);
