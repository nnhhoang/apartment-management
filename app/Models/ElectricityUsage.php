<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectricityUsage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'apartment_room_id',
        'usage_number',
        'input_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'input_date' => 'datetime',
    ];

    /**
     * Get the room that the electricity usage belongs to.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(ApartmentRoom::class, 'apartment_room_id');
    }
}