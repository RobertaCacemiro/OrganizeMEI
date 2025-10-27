<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui você pode registrar as rotas API do seu projeto.
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
