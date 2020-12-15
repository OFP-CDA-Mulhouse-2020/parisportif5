<?php

declare(strict_types=1);

namespace App\Entity;

interface FundStorageInterface
{
    public function convertToCurrencyUnit(int $amount): float;
    public function convertCurrencyUnitToStoredData(float $amount): int;
}
