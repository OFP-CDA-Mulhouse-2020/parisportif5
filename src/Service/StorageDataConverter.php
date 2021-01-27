<?php

declare(strict_types=1);

namespace App\Service;

use App\DataConverter\CommissionRateStorageInterface;
use App\DataConverter\DateTimeStorageInterface;
use App\DataConverter\FundsStorageInterface;
use App\DataConverter\OddsStorageInterface;

final class StorageDataConverter implements FundsStorageInterface, OddsStorageInterface, DateTimeStorageInterface, CommissionRateStorageInterface
{
    private DateTimeStorageDataConverter $dateTimeConverter;
    private FundsStorageDataConverter $fundsConverter;
    private OddsStorageDataConverter $oddsConverter;
    private CommissionRateStorageDataConverter $commissionRateConverter;

    public function __construct(
        DateTimeStorageDataConverter $dateTimeConverter,
        FundsStorageDataConverter $fundsConverter,
        OddsStorageDataConverter $oddsConverter,
        CommissionRateStorageDataConverter $commissionRateConverter
    ) {
        $this->dateTimeConverter = $dateTimeConverter;
        $this->fundsConverter = $fundsConverter;
        $this->oddsConverter = $oddsConverter;
        $this->commissionRateConverter = $commissionRateConverter;
    }

    public static function getStoredTimeZone(): string
    {
        return DateTimeStorageDataConverter::STORED_TIME_ZONE;
    }

    public function convertedToStoreDateTime(\DateTimeInterface $datetime): \DateTimeImmutable
    {
        return $this->dateTimeConverter->convertedToStoreDateTime($datetime);
    }

    public function setStoredTimezone(\DateTimeImmutable $datetimeImmutable): \DateTimeImmutable
    {
        return $this->dateTimeConverter->setStoredTimezone($datetimeImmutable);
    }

    public function convertToCurrencyUnit(int $amount): float
    {
        return $this->fundsConverter->convertToCurrencyUnit($amount);
    }

    public function convertCurrencyUnitToStoredData(float $amount): int
    {
        return $this->fundsConverter->convertCurrencyUnitToStoredData($amount);
    }

    public function convertToOddsMultiplier(int $odds): float
    {
        return $this->oddsConverter->convertToOddsMultiplier($odds);
    }

    public function convertOddsMultiplierToStoredData(float $odds): int
    {
        return $this->oddsConverter->convertOddsMultiplierToStoredData($odds);
    }

    public function convertToCommissionRate(int $commissionRate): float
    {
        return $this->commissionRateConverter->convertToCommissionRate($commissionRate);
    }

    public function convertCommissionRateToStoredData(float $commissionRate): int
    {
        return $this->commissionRateConverter->convertCommissionRateToStoredData($commissionRate);
    }
}
