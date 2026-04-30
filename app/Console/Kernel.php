<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;

class Kernel
{
    public function schedule(Schedule $schedule): void
    {
        $schedule->command('tasks:send-reminders')
            ->dailyAt('23:59')
            ->withoutOverlapping();

        $schedule->command('budgets:send-limit-alerts')
            ->dailyAt('09:00')
            ->withoutOverlapping();
    }
}