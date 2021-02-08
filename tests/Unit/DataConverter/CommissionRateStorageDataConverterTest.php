<?php

declare(strict_types=1);

namespace App\Tests\Unit\DataConverter;

use App\Service\CommissionRateStorageDataConverter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CommissionRateStorageDataConverter
 */
final class CommissionRateStorageDataConverterTest extends TestCase
{
    private function createCommissionRateStorageDataConverter(): CommissionRateStorageDataConverter
    {
        return new CommissionRateStorageDataConverter();
    }

    public function testMethodConvertToCommissionRateReturnValue(): void
    {
        $commissionRateStorageDataConverter = $this->createCommissionRateStorageDataConverter();
        $result = method_exists($commissionRateStorageDataConverter, 'convertToCommissionRate');
        $this->assertTrue($result);
        $result = $commissionRateStorageDataConverter->convertToCommissionRate('0.075');
        $this->assertIsFloat($result);
        $this->assertSame(7.5, $result);
    }

    public function testMethodConvertCommissionRateToStoredDataReturnValue(): void
    {
        $commissionRateStorageDataConverter = $this->createCommissionRateStorageDataConverter();
        $result = method_exists($commissionRateStorageDataConverter, 'convertCommissionRateToStoredData');
        $this->assertTrue($result);
        $result = $commissionRateStorageDataConverter->convertCommissionRateToStoredData(7.5);
        $this->assertIsString($result);
        $this->assertSame('0.075', $result);
    }
}
