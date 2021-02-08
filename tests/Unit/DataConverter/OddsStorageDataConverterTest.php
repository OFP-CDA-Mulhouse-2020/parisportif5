<?php

declare(strict_types=1);

namespace App\Tests\Unit\DataConverter;

use App\Service\OddsStorageDataConverter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OddsStorageDataConverter
 */
final class OddsStorageDataConverterTest extends TestCase
{
    private function createOddsStorageDataConverter(): OddsStorageDataConverter
    {
        return new OddsStorageDataConverter();
    }

    public function testMethodConvertToOddsMultiplierReturnValue(): void
    {
        $oddsStorageDataConverter = $this->createOddsStorageDataConverter();
        $result = method_exists($oddsStorageDataConverter, 'convertToOddsMultiplier');
        $this->assertTrue($result);
        $result = $oddsStorageDataConverter->convertToOddsMultiplier('1.5');
        $this->assertIsFloat($result);
        $this->assertSame(1.5, $result);
    }

    public function testMethodConvertOddsMultiplierToStoredDataReturnValue(): void
    {
        $oddsStorageDataConverter = $this->createOddsStorageDataConverter();
        $result = method_exists($oddsStorageDataConverter, 'convertOddsMultiplierToStoredData');
        $this->assertTrue($result);
        $result = $oddsStorageDataConverter->convertOddsMultiplierToStoredData(1.5);
        $this->assertIsString($result);
        $this->assertSame('1.5', $result);
    }
}
