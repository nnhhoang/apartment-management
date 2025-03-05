<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prefecture extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ward_id',
        'ward_name',
        'ward_name_en',
        'ward_level',
        'district_id',
        'district_name',
        'province_id',
        'province_name',
    ];

    /**
     * Get the apartments for the prefecture.
     */
    public function apartments(): HasMany
    {
        return $this->hasMany(Apartment::class, 'ward_id', 'ward_id');
    }
}