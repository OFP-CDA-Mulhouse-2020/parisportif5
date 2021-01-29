<?php

declare(strict_types=1);

namespace App\Service;

use App\DataConverter\OddsStorageInterface;

final class OddsStorageDataConverter implements OddsStorageInterface
{
    public function convertToOddsMultiplier(int $odds): float
    {
        return (float)($odds * 0.0001);
    }

    public function convertOddsMultiplierToStoredData(float $odds): int
    {
        return (int)($odds * 10000);
    }
}
