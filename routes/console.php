<?php

use App\Console\Commands\AbsentReminder;
use App\Console\Commands\CheckSessionAttendance;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the attendance check system to run every minute
Schedule::command(CheckSessionAttendance::class)->everyMinute();