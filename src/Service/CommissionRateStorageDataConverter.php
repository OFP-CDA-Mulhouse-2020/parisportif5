<?php

declare(strict_types=1);

namespace App\Service;

use App\DataConverter\CommissionRateStorageInterface;

final class CommissionRateStorageDataConverter implements CommissionRateStorageInterface
{
    public function convertToCommissionRate(string $commissionRate): float
    {
        return ((float)$commissionRate) * 100;
    }

    public function convertCommissionRateToStoredData(float $commissionRate): string
    {
        return (string)($commissionRate * 0.01);
    }
}
