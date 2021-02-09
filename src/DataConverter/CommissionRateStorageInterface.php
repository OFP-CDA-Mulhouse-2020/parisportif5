<?php

declare(strict_types=1);

namespace App\DataConverter;

interface CommissionRateStorageInterface
{
    public function convertToCommissionRate(string $commissionRate): float;
    public function convertCommissionRateToStoredData(float $commissionRate): string;
}
