<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'tel',
        'identity_card_number',
        'email',
    ];

    /**
     * Get the contracts for the tenant.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(TenantContract::class);
    }

    /**
     * Get the fee collections for the tenant.
     */
    public function feeCollections(): HasMany
    {
        return $this->hasMany(RoomFeeCollection::class);
    }
}