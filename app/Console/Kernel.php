<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Comandos Artisan registrados.
     */
    protected $commands = [
        \App\Console\Commands\PixGenerateCommand::class,
    ];

    /**
     * Agendamento de tarefas periódicas.
     */
    protected function schedule(Schedule $schedule)
    {
        // Executa o comando pix:gerar a cada 5 minutos
        $schedule->command('app:cron-test')->everyMinute();
        $schedule->command('pix:gerar')->everyFiveMinutes()->withoutOverlapping();
    }

    /**
     * Registra comandos do diretório Commands.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
