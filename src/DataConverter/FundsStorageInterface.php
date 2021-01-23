<?php

declare(strict_types=1);

namespace App\DataConverter;

interface FundsStorageInterface
{
    public function convertToCurrencyUnit(int $amount): float;
    public function convertCurrencyUnitToStoredData(float $amount): int;
}
