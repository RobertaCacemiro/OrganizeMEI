<?php

use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\TestCronCommand; // Importe o seu comando de teste
use App\Console\Commands\PixGenerateCommand; // Importe o seu comando PIX

// 1. Comando de Teste (para validação a cada minuto)
Artisan::command('app:cron-test', function () {
    // Para fins de teste, executamos a classe diretamente aqui
    (new TestCronCommand())->handle();
})->purpose('Teste de funcionamento do Cron Job no Railway.')
  ->everyMinute();

// 2. Comando PIX original (Seu comando de produção)
Artisan::command('pix:gerar', function () {
    // Executa a lógica da classe PixGenerateCommand
    (new PixGenerateCommand())->handle();
})->purpose('Gera um PIX e envia e-mail de cobrança.')
  ->everyFiveMinutes()
  ->withoutOverlapping(); // O withoutOverlapping é importante!
