<?php

declare(strict_types=1);

namespace App\Service;

use App\DataConverter\OddsStorageInterface;

final class OddsStorageDataConverter implements OddsStorageInterface
{
    public function convertToOddsMultiplier(string $odds): float
    {
        return (float)$odds;
    }

    public function convertOddsMultiplierToStoredData(float $odds): string
    {
        return (string)$odds;
    }
}
