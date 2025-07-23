<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProfileMeiController;


// Rotas para acessar o sistema
Route::get('/register', function () {
    return Inertia::render('Register');
});

Route::get('/login', function () {
    return Inertia::render('Login');
});

Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');

// Rotas post cadastro banco
Route::post('/register', [RegisterController::class, 'store']);

Route::post('/login', [LoginController::class, 'login']);


Route::middleware(['auth'])->group(function () {// rota teste
    Route::get('/', function () {
        return Inertia::render('Home');
    })->middleware(['auth', 'verified']);

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




    Route::get('/perfil-empresa', function () {
        return Inertia::render('PerfilEmpresa');
    });

    Route::get('/perfil-empresa', function () {
        return Inertia::render('PerfilEmpresa'); // componente Vue
    })->name('perfil.empresa');

    Route::get('/', function () {
        return Inertia::render('Home');
    })->name('home');






    Route::post('/cadastroCliente', [ClienteController::class, 'store']);


    Route::get('/perfil-mei', [ProfileMeiController::class, 'index'])->name('profile-mei.index');
    Route::post('/profile-mei/store', [ProfileMeiController::class, 'store']);
    Route::post('/profile-mei/{id}/update', [ProfileMeiController::class, 'update']);
});

