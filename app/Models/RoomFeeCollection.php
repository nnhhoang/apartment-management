<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomFeeCollection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_contract_id',
        'apartment_room_id',
        'tenant_id',
        'electricity_number_before',
        'electricity_number_after',
        'water_number_before',
        'water_number_after',
        'charge_date',
        'total_debt',
        'total_price',
        'total_paid',
        'fee_collection_uuid',
        'electricity_image',
        'water_image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'charge_date' => 'datetime',
    ];

    /**
     * Get the tenant that the fee collection belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the room that the fee collection belongs to.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(ApartmentRoom::class, 'apartment_room_id');
    }

    /**
     * Get the contract that the fee collection belongs to.
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(TenantContract::class, 'tenant_contract_id');
    }

    /**
     * Get the histories for the fee collection.
     */
    public function histories(): HasMany
    {
        return $this->hasMany(RoomFeeCollectionHistory::class);
    }
}