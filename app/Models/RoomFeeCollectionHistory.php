<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomFeeCollectionHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'room_fee_collection_id',
        'paid_date',
        'price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'paid_date' => 'datetime',
    ];

    /**
     * Get the fee collection that owns the history.
     */
    public function feeCollection(): BelongsTo
    {
        return $this->belongsTo(RoomFeeCollection::class, 'room_fee_collection_id');
    }
}