<?php

namespace App\Listeners;

use App\Events\ApartmentRoomCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Log;

class LogApartmentRoomCreation
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
    public function handle(ApartmentRoomCreated $event): void
    {
        Log::create([
            'user_id' => $event->apartmentRoom->apartment->user_id,
            'action' => 'apartment_room_created',
            'description' => 'Đã tạo phòng: ' . $event->apartmentRoom->room_number . ' trong tòa nhà: ' . $event->apartmentRoom->apartment->name,
        ]);
    }
}
