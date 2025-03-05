<?php

namespace App\Services;

use App\Models\TenantContract;

class FeeCalculationService
{
    /**
     * Calculate the fees based on contract and usage
     *
     * @param TenantContract $contract
     * @param int $electricityNumberBefore
     * @param int $electricityNumberAfter
     * @param int $waterNumberBefore
     * @param int $waterNumberAfter
     * @return array<string, int>
     */
    public function calculateFee(
        TenantContract $contract,
        int $electricityNumberBefore,
        int $electricityNumberAfter,
        int $waterNumberBefore,
        int $waterNumberAfter
    ): array {
        // Tính tiền thuê phòng cơ bản
        $roomFee = $contract->price;
        
        // Tính tiền điện
        $electricityUsage = $electricityNumberAfter - $electricityNumberBefore;
        $electricityFee = $this->calculateUtilityFee(
            $contract->electricity_pay_type,
            $electricityUsage,
            $contract->electricity_price,
            $contract->number_of_tenant_current
        );
        
        // Tính tiền nước
        $waterUsage = $waterNumberAfter - $waterNumberBefore;
        $waterFee = $this->calculateUtilityFee(
            $contract->water_pay_type,
            $waterUsage,
            $contract->water_price,
            $contract->number_of_tenant_current
        );
        
        // Tổng tiền
        $totalPrice = $roomFee + $electricityFee + $waterFee;
        
        return [
            'roomFee' => $roomFee,
            'electricityUsage' => $electricityUsage,
            'electricityFee' => $electricityFee,
            'waterUsage' => $waterUsage,
            'waterFee' => $waterFee,
            'totalPrice' => $totalPrice,
        ];
    }
    
    /**
     * Calculate utility fee based on pay type
     *
     * @param int $payType
     * @param int $usage
     * @param int $price
     * @param int $numberOfTenants
     * @return int
     */
    private function calculateUtilityFee(
        int $payType,
        int $usage,
        int $price,
        int $numberOfTenants
    ): int {
        return match ($payType) {
            TenantContract::PAY_TYPE_PER_PERSON => $price * $numberOfTenants, // Theo đầu người
            TenantContract::PAY_TYPE_FIXED => $price, // Trả cố định theo phòng
            TenantContract::PAY_TYPE_BY_USAGE => $price * $usage, // Tính theo lượng sử dụng
            default => 0,
        };
    }
}