<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ApartmentRoom extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'apartment_id',
        'room_number',
        'default_price',
        'max_tenant',
        'image',
    ];

    /**
     * Get the apartment that owns the room.
     */
    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    /**
     * Get the contracts for the room.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(TenantContract::class);
    }

    /**
     * Get the active contract for the room.
     */
    public function activeContract(): HasOne
    {
        return $this->hasOne(TenantContract::class)->whereNull('end_date')->latest();
    }

    /**
     * Get the fee collections for the room.
     */
    public function feeCollections(): HasMany
    {
        return $this->hasMany(RoomFeeCollection::class);
    }

    /**
     * Get the water usages for the room.
     */
    public function waterUsages(): HasMany
    {
        return $this->hasMany(WaterUsage::class);
    }

    /**
     * Get the electricity usages for the room.
     */
    public function electricityUsages(): HasMany
    {
        return $this->hasMany(ElectricityUsage::class);
    }
}