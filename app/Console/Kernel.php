<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Gửi thông báo tiền trọ chưa thanh toán đủ vào ngày 10 hàng tháng
        $schedule->command('rent:unpaid-notifications')
                ->monthlyOn(10, '08:00')
                ->appendOutputTo(storage_path('logs/unpaid-notifications.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}