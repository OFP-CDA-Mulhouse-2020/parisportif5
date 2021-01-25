<?php

declare(strict_types=1);

namespace App\DataConverter;

final class CommissionRateStorageDataConverter implements CommissionRateStorageInterface
{
    public function convertToCommissionRate(int $commissionRate): float
    {
        return floatVal($commissionRate * 0.0001);
    }

    public function convertCommissionRateToStoredData(float $commissionRate): int
    {
        return intVal($commissionRate * 10000);
    }
}
