<?php

declare(strict_types=1);

namespace App\Tests\Unit\DataConverter;

use App\Service\CommissionRateStorageDataConverter;
use App\Service\DateTimeStorageDataConverter;
use App\Service\FundsStorageDataConverter;
use App\Service\StorageDataConverter;
use App\Service\OddsStorageDataConverter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \StorageDataConverter
 */
final class StorageDataConverterTest extends TestCase
{
    private function createStorageDataConverter(): StorageDataConverter
    {
        return new StorageDataConverter(
            new DateTimeStorageDataConverter(),
            new FundsStorageDataConverter(),
            new OddsStorageDataConverter(),
            new CommissionRateStorageDataConverter()
        );
    }

    public function testConstantStoredTimeZone(): void
    {
        $storageDataConverter = $this->createStorageDataConverter();
        $result = method_exists($storageDataConverter, 'getStoredTimeZone');
        $this->assertTrue($result);
        $this->assertIsString($storageDataConverter::getStoredTimeZone());
        $this->assertSame("UTC", $storageDataConverter::getStoredTimeZone());
    }

    /**
     * @dataProvider methodConvertedToStoreDateTimeProvider
     */
    public function testMethodConvertedToStoreDateTimeReturnValue(\DateTimeInterface $datetime): void
    {
        $storageDataConverter = $this->createStorageDataConverter();
        $result = method_exists($storageDataConverter, 'convertedToStoreDateTime');
        $this->assertTrue($result);
        $result = $storageDataConverter->convertedToStoreDateTime($datetime);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result);
    }

    public function methodConvertedToStoreDateTimeProvider(): array
    {
        return [
            [new \DateTimeImmutable("2021-01-20 00:00:00", new \DateTimeZone("UTC"))],
            [new \DateTimeImmutable("2021-01-20 00:00:00", new \DateTimeZone("Europe/Paris"))],
            [new \DateTime("2021-01-20 00:00:00", new \DateTimeZone("UTC"))],
            [new \DateTime("2021-01-20 00:00:00", new \DateTimeZone("Europe/Paris"))]
        ];
    }

    /**
     * @dataProvider methodSetStoredTimezoneProvider
     */
    public function testMethodSetStoredTimezoneReturnValue(\DateTimeImmutable $datetimeImmutable): void
    {
        $storageDataConverter = $this->createStorageDataConverter();
        $result = method_exists($storageDataConverter, 'setStoredTimezone');
        $this->assertTrue($result);
        $result = $storageDataConverter->setStoredTimezone($datetimeImmutable);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result);
        $this->assertInstanceOf(\DateTimeZone::class, $result->getTimezone());
        $this->assertSame("UTC", $result->getTimezone()->getName());
    }

    public function methodSetStoredTimezoneProvider(): array
    {
        return [
            [new \DateTimeImmutable("2021-01-20 00:00:00", new \DateTimeZone("UTC"))],
            [new \DateTimeImmutable("2021-01-20 00:00:00", new \DateTimeZone("Europe/Paris"))],
            [new \DateTimeImmutable("2021-01-20 00:00:00", new \DateTimeZone("America/New_York"))]
        ];
    }

    public function testMethodConvertToOddsMultiplierReturnValue(): void
    {
        $storageDataConverter = $this->createStorageDataConverter();
        $result = method_exists($storageDataConverter, 'convertToOddsMultiplier');
        $this->assertTrue($result);
        $result = $storageDataConverter->convertToOddsMultiplier('1.5');
        $this->assertIsFloat($result);
        $this->assertSame(1.5, $result);
    }

    public function testMethodConvertOddsMultiplierToStoredDataReturnValue(): void
    {
        $storageDataConverter = $this->createStorageDataConverter();
        $result = method_exists($storageDataConverter, 'convertOddsMultiplierToStoredData');
        $this->assertTrue($result);
        $result = $storageDataConverter->convertOddsMultiplierToStoredData(1.5);
        $this->assertIsString($result);
        $this->assertSame('1.5', $result);
    }

    public function testMethodConvertToCurrencyUnitReturnValue(): void
    {
        $storageDataConverter = $this->createStorageDataConverter();
        $result = method_exists($storageDataConverter, 'convertToCurrencyUnit');
        $this->assertTrue($result);
        $result = $storageDataConverter->convertToCurrencyUnit(500);
        $this->assertIsFloat($result);
        $this->assertSame(5.0, $result);
    }

    public function testMethodConvertCurrencyUnitToStoredDataReturnValue(): void
    {
        $storageDataConverter = $this->createStorageDataConverter();
        $result = method_exists($storageDataConverter, 'convertCurrencyUnitToStoredData');
        $this->assertTrue($result);
        $result = $storageDataConverter->convertCurrencyUnitToStoredData(5.0);
        $this->assertIsInt($result);
        $this->assertSame(500, $result);
    }

    public function testMethodConvertToCommissionRateReturnValue(): void
    {
        $storageDataConverter = $this->createStorageDataConverter();
        $result = method_exists($storageDataConverter, 'convertToCommissionRate');
        $this->assertTrue($result);
        $result = $storageDataConverter->convertToCommissionRate('0.075');
        $this->assertIsFloat($result);
        $this->assertSame(7.5, $result);
    }

    public function testMethodConvertCommissionRateToStoredDataReturnValue(): void
    {
        $storageDataConverter = $this->createStorageDataConverter();
        $result = method_exists($storageDataConverter, 'convertCommissionRateToStoredData');
        $this->assertTrue($result);
        $result = $storageDataConverter->convertCommissionRateToStoredData(7.5);
        $this->assertIsString($result);
        $this->assertSame('0.075', $result);
    }
}
