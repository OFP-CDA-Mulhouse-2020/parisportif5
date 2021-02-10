<?php

declare(strict_types=1);

namespace App\Service;

use App\DataConverter\CommissionRateStorageInterface;
use App\DataConverter\FundsStorageInterface;
use App\DataConverter\OddsStorageInterface;

final class StorageDataConverter implements FundsStorageInterface, OddsStorageInterface, CommissionRateStorageInterface
{
    private FundsStorageDataConverter $fundsConverter;
    private OddsStorageDataConverter $oddsConverter;
    private CommissionRateStorageDataConverter $commissionRateConverter;

    public function __construct(
        FundsStorageDataConverter $fundsConverter,
        OddsStorageDataConverter $oddsConverter,
        CommissionRateStorageDataConverter $commissionRateConverter
    ) {
        $this->fundsConverter = $fundsConverter;
        $this->oddsConverter = $oddsConverter;
        $this->commissionRateConverter = $commissionRateConverter;
    }

    public function convertToCurrencyUnit(int $amount): float
    {
        return $this->fundsConverter->convertToCurrencyUnit($amount);
    }

    public function convertCurrencyUnitToStoredData(float $amount): int
    {
        return $this->fundsConverter->convertCurrencyUnitToStoredData($amount);
    }

    public function convertToOddsMultiplier(string $odds): float
    {
        return $this->oddsConverter->convertToOddsMultiplier($odds);
    }

    public function convertOddsMultiplierToStoredData(float $odds): string
    {
        return $this->oddsConverter->convertOddsMultiplierToStoredData($odds);
    }

    public function convertToCommissionRate(string $commissionRate): float
    {
        return $this->commissionRateConverter->convertToCommissionRate($commissionRate);
    }

    public function convertCommissionRateToStoredData(float $commissionRate): string
    {
        return $this->commissionRateConverter->convertCommissionRateToStoredData($commissionRate);
    }
}
