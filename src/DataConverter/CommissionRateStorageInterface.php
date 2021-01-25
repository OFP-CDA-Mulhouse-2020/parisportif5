<?php

declare(strict_types=1);

namespace App\DataConverter;

interface CommissionRateStorageInterface
{
    public function convertToCommissionRate(int $commissionRate): float;
    public function convertCommissionRateToStoredData(float $commissionRate): int;
}
