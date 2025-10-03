<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestCronCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cron-test'; // Comando simples e único

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Teste de funcionamento do Cron Job no Railway.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('[' . now()->toDateTimeString() . '] O Cron Job de Teste FOI EXECUTADO com SUCESSO!');

        // Adicionando um log de erro para garantir que a saída de erro também funcione
        $this->error('Este é um log de erro de teste. Se aparecer, está funcionando.');

        return Command::SUCCESS;
    }
}
