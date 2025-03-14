<?php

namespace App\Policies;

use App\Models\RoomFeeCollection;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomFeeCollectionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RoomFeeCollection $roomFeeCollection): bool
    {
        return $user->id === $roomFeeCollection->room->apartment->user_id;
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
    public function update(User $user, RoomFeeCollection $roomFeeCollection): bool
    {
        return $user->id === $roomFeeCollection->room->apartment->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RoomFeeCollection $roomFeeCollection): bool
    {
        return $user->id === $roomFeeCollection->room->apartment->user_id;
    }
}