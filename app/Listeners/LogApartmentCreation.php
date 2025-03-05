<?php

namespace App\Listeners;

use App\Events\ApartmentCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Log;

class LogApartmentCreation
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
    public function handle(ApartmentCreated $event): void
    {
        Log::create([
            'user_id' => $event->apartment->user_id,
            'action' => 'apartment_created',
            'description' => 'Đã tạo tòa nhà: ' . $event->apartment->name,
        ]);
    }
}
