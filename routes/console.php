<?php

use App\Console\Commands\BackupDatabase;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();






Artisan::command('database:backup', function () {
    $this->call(BackupDatabase::class);
})->purpose('Realiza un respaldo de la base de datos MySQL usando mysqldump')->everyMinute();