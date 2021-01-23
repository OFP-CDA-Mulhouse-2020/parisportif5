<?php

declare(strict_types=1);

namespace App\DataConverter;

interface OddsStorageInterface
{
    public function convertToOddsMultiplier(int $odds): float;
    public function convertOddsMultiplierToStoredData(float $odds): int;
}
