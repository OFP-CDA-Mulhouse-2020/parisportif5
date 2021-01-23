<?php

declare(strict_types=1);

namespace App\DataConverter;

final class OddsStorageDataConverter implements OddsStorageInterface
{
    public function convertToOddsMultiplier(int $odds): float
    {
        return floatVal($odds * 0.0001);
    }

    public function convertOddsMultiplierToStoredData(float $odds): int
    {
        return intVal($odds * 10000);
    }
}
