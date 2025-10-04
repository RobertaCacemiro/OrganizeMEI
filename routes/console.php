<?php

use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\PixGenerateCommand;

Artisan::command('pix:gerar', function () {

    (new PixGenerateCommand())->handle();
})->purpose('Gera um PIX e envia e-mail de cobrança.')
    ->everyFiveMinutes()
    ->withoutOverlapping();
