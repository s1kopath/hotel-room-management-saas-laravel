<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Archive admin reservation history monthly (on the 1st of each month at 2 AM)
Schedule::command('admin-reservations:archive --days=30')
    ->monthlyOn(1, '02:00')
    ->timezone('UTC')
    ->description('Archive admin reservation history records older than 30 days');
