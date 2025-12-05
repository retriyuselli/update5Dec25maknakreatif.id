<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule untuk auto-generate Account Manager targets
Schedule::command('targets:generate --auto-12-months')
    ->yearlyOn(1, 1, '00:00') // Jalankan setiap 1 Januari jam 00:00
    ->description('Auto-generate Account Manager targets for new year');

Schedule::command('targets:generate --update')
    ->daily() // Update achieved amounts setiap hari
    ->description('Update Account Manager targets achieved amounts');
