<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('backup:run')->at('00:00');
Schedule::command('baricode:cleanup-soft-deleted')->at('00:00');

// WhatsApp Group Management Commands
Schedule::command('whatsapp-groups:send-daily-quotes')->dailyAt('15:00');
Schedule::command('whatsapp-groups:send-daily-quotes')->dailyAt('06:00');