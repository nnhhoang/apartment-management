<?php

namespace App\Providers;

use App\Events\ApartmentCreated;
use App\Events\ApartmentRoomCreated;
use App\Events\UnpaidRentNotificationSent;
use App\Listeners\LogApartmentCreation;
use App\Listeners\LogApartmentRoomCreation;
use App\Listeners\LogUnpaidRentNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ApartmentCreated::class => [
            LogApartmentCreation::class,
        ],
        ApartmentRoomCreated::class => [
            LogApartmentRoomCreation::class,
        ],
        UnpaidRentNotificationSent::class => [
            LogUnpaidRentNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Không cần gọi parent::boot() trong Laravel 11
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}