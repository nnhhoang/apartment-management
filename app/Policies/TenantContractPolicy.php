<?php

namespace App\Policies;

use App\Models\TenantContract;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantContractPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TenantContract $tenantContract): bool
    {
        return $user->id === $tenantContract->room->apartment->user_id;
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
    public function update(User $user, TenantContract $tenantContract): bool
    {
        return $user->id === $tenantContract->room->apartment->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TenantContract $tenantContract): bool
    {
        return $user->id === $tenantContract->room->apartment->user_id;
    }
}