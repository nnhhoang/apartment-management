<?php

namespace App\Providers;

use App\Models\Apartment;
use App\Models\ApartmentRoom;
use App\Models\RoomFeeCollection;
use App\Models\Tenant;
use App\Models\TenantContract;
use App\Policies\ApartmentPolicy;
use App\Policies\TenantPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Apartment::class => ApartmentPolicy::class,
        Tenant::class => TenantPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Không cần gọi $this->registerPolicies() trong Laravel 11

        // Gate để kiểm tra quyền truy cập vào phòng trọ
        Gate::define('view-room', function ($user, ApartmentRoom $room) {
            return $user->id === $room->apartment->user_id;
        });

        Gate::define('update-room', function ($user, ApartmentRoom $room) {
            return $user->id === $room->apartment->user_id;
        });

        Gate::define('delete-room', function ($user, ApartmentRoom $room) {
            return $user->id === $room->apartment->user_id;
        });

        // Gate để kiểm tra quyền truy cập vào hợp đồng
        Gate::define('view-contract', function ($user, TenantContract $contract) {
            return $user->id === $contract->room->apartment->user_id;
        });

        Gate::define('update-contract', function ($user, TenantContract $contract) {
            return $user->id === $contract->room->apartment->user_id;
        });

        // Gate để kiểm tra quyền truy cập vào khoản thu tiền
        Gate::define('view-fee', function ($user, RoomFeeCollection $fee) {
            return $user->id === $fee->room->apartment->user_id;
        });

        Gate::define('update-fee', function ($user, RoomFeeCollection $fee) {
            return $user->id === $fee->room->apartment->user_id;
        });

        Gate::define('delete-fee', function ($user, RoomFeeCollection $fee) {
            return $user->id === $fee->room->apartment->user_id;
        });
    }
}