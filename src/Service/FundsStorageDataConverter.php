<?php

declare(strict_types=1);

namespace App\Service;

use App\DataConverter\FundsStorageInterface;

final class FundsStorageDataConverter implements FundsStorageInterface
{
    public function convertToCurrencyUnit(int $amount): float
    {
        return (float)($amount * 0.01);
    }

    public function convertCurrencyUnitToStoredData(float $amount): int
    {
        return (int)($amount * 100);
    }
}
