<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Comandos Artisan registrados.
     * * NOTA: Este array foi limpo. O Laravel carregará automaticamente
     * os comandos da pasta Commands no método commands().
     * Se você precisar de comandos de pacotes de terceiros, eles
     * são incluídos automaticamente.
     */
    protected $commands = [
        // O Laravel 10/11 carrega comandos automaticamente via commands().
        // Deixamos este array vazio ou com a definição de comandos de
        // pacotes de terceiros, se necessário.
    ];

    /**
     * Agendamento de tarefas periódicas.
     */
    protected function schedule(Schedule $schedule)
    {
        // DEIXE ESTE MÉTODO VAZIO
        // Não inclua nada aqui.
    }

    /**
     * Registra comandos do diretório Commands.
     */
    protected function commands()
    {
        // O método 'load' garante que suas classes 'PixGenerateCommand'
        // e 'TestCronCommand' sejam carregadas corretamente.
        $this->load(__DIR__ . '/Commands');

        // Mantemos a rota de console para o caso de comandos definidos lá.
        require base_path('routes/console.php');
    }
}
