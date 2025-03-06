<?php

namespace App\Policies;

use App\Models\ApartmentRoom;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApartmentRoomPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ApartmentRoom $apartmentRoom): bool
    {
        return $user->id === $apartmentRoom->apartment->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ApartmentRoom $apartmentRoom): bool
    {
        return $user->id === $apartmentRoom->apartment->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ApartmentRoom $apartmentRoom): bool
    {
        return $user->id === $apartmentRoom->apartment->user_id;
    }
}