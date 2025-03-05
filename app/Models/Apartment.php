<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apartment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'province_id',
        'district_id',
        'ward_id',
        'address',
        'image',
    ];

    /**
     * Get the user that owns the apartment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the rooms for the apartment.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(ApartmentRoom::class);
    }

    /**
     * Get the prefecture associated with the apartment.
     */
    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class, 'ward_id', 'ward_id');
    }
}
