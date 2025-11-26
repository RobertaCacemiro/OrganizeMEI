<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\{
    RegisterController,
    ProfileMeiController,
    ClientController,
    TransactionController,
    ChargeController,
    CategoriesController,
    PaymentController,
    ProofController,
    MeiPixKeyController,
    DashboardController,
    ChartController
};

Route::get('/pix-keys', [MeiPixKeyController::class, 'index'])
    ->middleware(['auth'])
    ->name('pix-keys.index');

// Rotas para acessar o sistema
Route::get('/register', function () {
    return Inertia::render('Register');
})->name('register');

Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');

Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); // Já nomeada

Route::get('/comprovante/{key}', [ProofController::class, 'show'])->name('comprovante.show');
Route::post('/comprovante/{key}', [ProofController::class, 'upload'])->name('comprovante.upload');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    Route::get('/perfil-mei', [ProfileMeiController::class, 'index'])->name('profile-mei.index');
    Route::post('/profile-mei/store', [ProfileMeiController::class, 'store']);
    Route::post('/profile-mei/{id}/update', [ProfileMeiController::class, 'update']);

    Route::middleware(['require.mei'])->group(function () {

        Route::get('/', function () {
            return Inertia::render(component: 'Home');
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

        Route::get('/pagamentos', function () {
            return Inertia::render('Pagamentos');
        });

        Route::get('/upgrade', function () {
            return Inertia::render('Upgrade');
        })->name('upgrade');

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


        Route::get('/clientes', [ClientController::class, 'index'])->name('clientes.index');
        Route::post('/clientes/store', [ClientController::class, 'store']);
        Route::post('/clientes/{id}/update', [ClientController::class, 'update']);
        Route::delete('/clientes/{id}', [ClientController::class, 'destroy']);

        Route::get('/financeiro', [TransactionController::class, 'index'])->name('financeiro.index');
        Route::post('/financeiro/store', [TransactionController::class, 'store']);
        Route::get('/financeiro/{id}/edit', [TransactionController::class, 'edit']);
        Route::post('/financeiro/{id}/update', [TransactionController::class, 'update']);
        Route::delete('/financeiro/{id}', [TransactionController::class, 'destroy']);

        Route::post('/categorias/store', [CategoriesController::class, 'store']);

        Route::get('/cobrancas', [ChargeController::class, 'index'])->name('cobrancas.index');
        Route::post('/cobrancas/store', [ChargeController::class, 'store']);
        Route::post('/cobrancas/{id}/update', [ChargeController::class, 'update']);
        Route::delete('/cobrancas/{id}', [ChargeController::class, 'destroy']);

        Route::get('/pagamentos', [PaymentController::class, 'index'])->name('pagamentos.index');

        Route::get('/comprovante/{key}', [ProofController::class, 'show'])->name('comprovante.show');

        Route::get('/api/pix-keys', [MeiPixKeyController::class, 'index'])
            ->name('pix.keys.fetch');
        Route::delete('/pix-keys/{id}', [MeiPixKeyController::class, 'destroy']);
        Route::put('/pix-keys/{id}/activate', [MeiPixKeyController::class, 'update']);
        Route::post('/pix-keys/store', [MeiPixKeyController::class, 'store']);

        Route::get('/', [DashboardController::class, 'index'])->name('home');

        Route::get('/upgrade', function () {
            return Inertia::render('Upgrade');
        })->name('upgrade');

        Route::middleware(['auth'])->get('/charts/monthly-balance', [ChartController::class, 'monthlyBalance'])->name('charts.monthly_balance');
        Route::middleware(['auth'])->get('/charts/billing-status', [ChartController::class, 'billingStatus'])->name('charts.billing_status');
        Route::middleware(['auth'])->get('/charts/income-categories', [ChartController::class, 'incomeCategories'])->name('charts.income_categories');
        Route::middleware(['auth'])->get('/charts/expense-categories', [ChartController::class, 'expenseCategories'])->name('charts.expense_categories');

    });

});


