<?php

declare(strict_types=1);

namespace App\Tests\Unit\DataConverter;

use App\Service\DateTimeStorageDataConverter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \DateTimeStorageDataConverter
 */
final class DateTimeStorageDataConverterTest extends TestCase
{
    private function createDateTimeStorageDataConverter(): DateTimeStorageDataConverter
    {
        return new DateTimeStorageDataConverter();
    }

    public function testConstantStoredTimeZone(): void
    {
        $dateTimeStorageDataConverter = $this->createDateTimeStorageDataConverter();
        $className = get_class($dateTimeStorageDataConverter);
        $result = defined("$className::STORED_TIME_ZONE");
        $this->assertTrue($result);
        $this->assertIsString($dateTimeStorageDataConverter::STORED_TIME_ZONE);
        $this->assertSame("UTC", $dateTimeStorageDataConverter::STORED_TIME_ZONE);
    }

    /**
     * @dataProvider methodConvertedToStoreDateTimeProvider
     */
    public function testMethodConvertedToStoreDateTimeReturnValue(\DateTimeInterface $datetime): void
    {
        $dateTimeStorageDataConverter = $this->createDateTimeStorageDataConverter();
        $result = method_exists($dateTimeStorageDataConverter, 'convertedToStoreDateTime');
        $this->assertTrue($result);
        $result = $dateTimeStorageDataConverter->convertedToStoreDateTime($datetime);
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
        $dateTimeStorageDataConverter = $this->createDateTimeStorageDataConverter();
        $result = method_exists($dateTimeStorageDataConverter, 'setStoredTimezone');
        $this->assertTrue($result);
        $result = $dateTimeStorageDataConverter->setStoredTimezone($datetimeImmutable);
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
}
