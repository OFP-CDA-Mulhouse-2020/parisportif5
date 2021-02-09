<?php

declare(strict_types=1);

namespace App\DataConverter;

interface OddsStorageInterface
{
    public function convertToOddsMultiplier(string $odds): float;
    public function convertOddsMultiplierToStoredData(float $odds): string;
}
