<?php

namespace App\Listeners;

use App\Events\UnpaidRentNotificationSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Log;

class LogUnpaidRentNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UnpaidRentNotificationSent $event): void
    {
        Log::create([
            'user_id' => $event->user->id,
            'action' => 'unpaid_rent_notification',
            'description' => 'Đã gửi thông báo cho ' . $event->unpaidCollections->count() . ' phòng chưa thanh toán đủ tiền',
        ]);
    }
}
