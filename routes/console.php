<?php

use App\Console\Commands\EnviarRecordatoriosEventos;
use App\Console\Commands\GenerarEventosRecurrentes;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(GenerarEventosRecurrentes::class)->daily();
Schedule::command(EnviarRecordatoriosEventos::class)->hourly();
