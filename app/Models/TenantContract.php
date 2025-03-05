<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenantContract extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'apartment_room_id',
        'tenant_id',
        'pay_period',
        'price',
        'electricity_pay_type',
        'electricity_price',
        'electricity_number_start',
        'water_pay_type',
        'water_price',
        'water_number_start',
        'number_of_tenant_current',
        'note',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Kỳ hạn thanh toán
    public const PAY_PERIOD_MONTHLY = 1;
    public const PAY_PERIOD_QUARTERLY = 3;
    public const PAY_PERIOD_BIANNUAL = 6;
    public const PAY_PERIOD_ANNUAL = 12;

    // Phương thức tính tiền điện/nước
    public const PAY_TYPE_PER_PERSON = 1;  // Theo đầu người
    public const PAY_TYPE_FIXED = 2;       // Cố định theo phòng
    public const PAY_TYPE_BY_USAGE = 3;    // Theo lượng sử dụng

    /**
     * Get the room that the contract belongs to.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(ApartmentRoom::class, 'apartment_room_id');
    }

    /**
     * Get the tenant that the contract belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the fee collections for the contract.
     */
    public function feeCollections(): HasMany
    {
        return $this->hasMany(RoomFeeCollection::class);
    }
}