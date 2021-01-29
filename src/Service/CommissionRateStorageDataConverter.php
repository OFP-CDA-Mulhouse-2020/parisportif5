<?php

declare(strict_types=1);

namespace App\Service;

use App\DataConverter\CommissionRateStorageInterface;

final class CommissionRateStorageDataConverter implements CommissionRateStorageInterface
{
    public function convertToCommissionRate(int $commissionRate): float
    {
        return (float)($commissionRate * 0.0001);
    }

    public function convertCommissionRateToStoredData(float $commissionRate): int
    {
        return (int)($commissionRate * 10000);
    }
}
