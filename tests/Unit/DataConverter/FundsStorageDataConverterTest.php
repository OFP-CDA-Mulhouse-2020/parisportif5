<?php

declare(strict_types=1);

namespace App\Tests\Unit\DataConverter;

use App\Service\FundsStorageDataConverter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FundsStorageDataConverter
 */
final class FundsStorageDataConverterTest extends TestCase
{
    private function createFundsStorageDataConverter(): FundsStorageDataConverter
    {
        return new FundsStorageDataConverter();
    }

    public function testMethodConvertToCurrencyUnitReturnValue(): void
    {
        $fundsStorageDataConverter = $this->createFundsStorageDataConverter();
        $result = method_exists($fundsStorageDataConverter, 'convertToCurrencyUnit');
        $this->assertTrue($result);
        $result = $fundsStorageDataConverter->convertToCurrencyUnit(500);
        $this->assertIsFloat($result);
        $this->assertSame(5.0, $result);
    }

    public function testMethodConvertCurrencyUnitToStoredDataReturnValue(): void
    {
        $fundsStorageDataConverter = $this->createFundsStorageDataConverter();
        $result = method_exists($fundsStorageDataConverter, 'convertCurrencyUnitToStoredData');
        $this->assertTrue($result);
        $result = $fundsStorageDataConverter->convertCurrencyUnitToStoredData(5.0);
        $this->assertIsInt($result);
        $this->assertSame(500, $result);
    }
}
