<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Billing;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BillingTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    //throw new \Exception($violations);
    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidBilling(): Billing
    {
        $billing = new Billing();
        $billing
            ->setFirstName("Dupont")
            ->setLastName("Tintin")
            ->setAddress("1 avenue st martin")
            ->setCity("Colmar")
            ->setPostcode("68000")
            ->setCountry("FR")
            ->setDesignation("paris n1")
            ->setOrderNumber(1)
            ->setInvoiceNumber(1)
            ->setAmount(5);
        return $billing;
    }

    /**
     * @dataProvider firstNameCompatibleProvider
     */
    public function testFirstNameCompatible(string $name)
    {
        $billing = $this->createValidBilling();
        $billing->setFirstName($name);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
    }

    public function firstNameCompatibleProvider(): array
    {
        return [
            ["Anaïs"],
            ["édouârd"],
            ["Jean-Marc de l'Atour"],
            ["ggg"]
        ];
    }

    /**
     * @dataProvider lastNameCompatibleProvider
     */
    public function testLastNameCompatible(string $name)
    {
        $billing = $this->createValidBilling();
        $billing->setLastName($name);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
    }

    public function lastNameCompatibleProvider(): array
    {
        return [
            ["Anaïs"],
            ["édouârd"],
            ["Jean-Marc de l'Atour"],
            ["ggg"]
        ];
    }

    /**
     * @dataProvider lastNameUncompatibleProvider
     */
    public function testLastNameUncompatible(string $name)
    {
        $billing = $this->createValidBilling();
        $billing->setLastName($name);
        $violations = $this->validator->validate($billing);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function lastNameUncompatibleProvider(): array
    {
        return [
            ["gabin#"],
            ["fabien25"],
            [""],
            ["    "],
            ["g"],
            ["monsieurdontlenomestbientroplong"]
        ];
    }

    /**
     * @dataProvider firstNameUncompatibleProvider
     */
    public function testFirstNameUncompatibleException(string $name)
    {
        $billing = $this->createValidBilling();
        $billing->setFirstName($name);
        $violations = $this->validator->validate($billing);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function firstNameUncompatibleProvider(): array
    {
        return [
            ["gabin#"],
            ["fabien25"],
            [""],
            ["    "],
            ["g"],
            ["monsieurdontleprénomestbientroplong"]
        ];
    }

    /**
     * @dataProvider addressCompatibleProvider
     */
    public function testAddressCompatible(string $address)
    {
        $billing = $this->createValidBilling();
        $billing->setAddress($address);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
    }

    public function addressCompatibleProvider(): array
    {
        return [
            ["5, rue Jean-Gabin"],
            ["lieu-dit LeFabien"],
            ["Rue de l'Abbaye"],
            ["ggg"]
        ];
    }

    /**
     * @dataProvider addressUncompatibleProvider
     */
    public function testAddressUncompatible(string $address)
    {
        $billing = $this->createValidBilling();
        $billing->setAddress($address);
        $violations = $this->validator->validate($billing);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function addressUncompatibleProvider(): array
    {
        return [
            ["5, rue Jean-Gabin#"],
            ["lieu-dit LeFabien@"],
            ["Rue de l`Abbaye"],
            [""],
            ["    "]
        ];
    }

    /**
     * @dataProvider cityCompatibleProvider
     */
    public function testCityCompatible(string $city)
    {
        $billing = $this->createValidBilling();
        $billing->setCity($city);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
    }

    public function cityCompatibleProvider(): array
    {
        return [
            ["Saint-Jean de L'Arche"],
            ["Paris"],
            ["londre"]
        ];
    }

    /**
     * @dataProvider cityUncompatibleProvider
     */
    public function testCityUncompatible(string $city)
    {
        $billing = $this->createValidBilling();
        $billing->setCity($city);
        $violations = $this->validator->validate($billing);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function cityUncompatibleProvider(): array
    {
        return [
            ["1the village"],
            ["P@ris"],
            ["londre,"],
            [''],
            ['  ']
        ];
    }

    /**
     * @dataProvider postcodeCompatibleProvider
     */
    public function testPostcodeCompatible(string $postcode)
    {
        $billing = $this->createValidBilling();
        $billing->setPostcode($postcode);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
    }

    public function postcodeCompatibleProvider(): array
    {
        return [
            ["68000"],
            ["CP-Index 7000"]
        ];
    }

    /**
     * @dataProvider postcodeUncompatibleProvider
     */
    public function testPostcodeUncompatible(string $postcode)
    {
        $billing = $this->createValidBilling();
        $billing->setPostcode($postcode);
        $violations = $this->validator->validate($billing);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function postcodeUncompatibleProvider(): array
    {
        return [
            ["68000@"],
            ["CP'Index 7000"],
            [''],
            ['  ']
        ];
    }

    /**
     * @dataProvider countryCompatibleProvider
     * ISO 3166-1 alpha-2 => 2 lettres majuscules
     */
    public function testCountryCompatible(string $country)
    {
        $billing = $this->createValidBilling();
        $billing->setCountry($country);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
    }

    public function countryCompatibleProvider(): array
    {
        return [
            ["FR"],
            ["DE"]
        ];
    }

    /**
     * @dataProvider countryUncompatibleProvider
     */
    public function testCountryUncompatible(string $country)
    {
        $billing = $this->createValidBilling();
        $billing->setCountry($country);
        $violations = $this->validator->validate($billing);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function countryUncompatibleProvider(): array
    {
        return [
            ["XY"],
            ["FRA"],
            ["France"],
            ["fr"],
            [''],
            ['   ']
        ];
    }

    /**
     * @dataProvider designationCompatibleProvider
     */
    public function testDesignationCompatible(string $designation)
    {
        $billing = $this->createValidBilling();
        $billing->setDesignation($designation);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
    }

    public function designationCompatibleProvider(): array
    {
        return [
            ["paris sur le match PSG contre Truc, machin vainqueur"],
            ["PSG 1 <()[{]}>=+-*/\_?!;,:"]
        ];
    }

    public function testDesignationUncompatible()
    {
        $designation1 = '';
        $designation2 = '   ';
        $billing = $this->createValidBilling();
        $billing->setDesignation($designation1);
        $violations = $this->validator->validate($billing);
        $this->assertCount(1, $violations);
        $billing->setDesignation($designation2);
        $violations = $this->validator->validate($billing);
        $this->assertCount(1, $violations);
    }

    public function testOrderNumberCompatible()
    {
        $orderNumber1 = 1;
        $orderNumber2 = 1000000000;
        $billing = $this->createValidBilling();
        $billing->setOrderNumber($orderNumber1);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
        $billing->setOrderNumber($orderNumber2);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
    }

    public function testOrderNumberUncompatible()
    {
        $orderNumber1 = 0;
        $orderNumber2 = -1;
        $billing = $this->createValidBilling();
        $billing->setOrderNumber($orderNumber1);
        $violations = $this->validator->validate($billing);
        $this->assertCount(1, $violations);
        $billing->setOrderNumber($orderNumber2);
        $violations = $this->validator->validate($billing);
        $this->assertCount(1, $violations);
    }

    public function testInvoiceNumberCompatible()
    {
        $invoiceNumber1 = 1;
        $invoiceNumber2 = 1000000000;
        $billing = $this->createValidBilling();
        $billing->setInvoiceNumber($invoiceNumber1);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
        $billing->setInvoiceNumber($invoiceNumber2);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
    }

    public function testInvoiceNumberUncompatible()
    {
        $invoiceNumber1 = 0;
        $invoiceNumber2 = -1;
        $billing = $this->createValidBilling();
        $billing->setInvoiceNumber($invoiceNumber1);
        $violations = $this->validator->validate($billing);
        $this->assertCount(1, $violations);
        $billing->setInvoiceNumber($invoiceNumber2);
        $violations = $this->validator->validate($billing);
        $this->assertCount(1, $violations);
    }

    public function testAmountCompatible()
    {
        $amount1 = 0;
        $amount2 = 1000000000;
        $billing = $this->createValidBilling();
        $billing->setAmount($amount1);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
        $billing->setAmount($amount2);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
    }

    public function testAmountUncompatible()
    {
        $amount = -1;
        $billing = $this->createValidBilling();
        $billing->setAmount($amount);
        $violations = $this->validator->validate($billing);
        $this->assertCount(1, $violations);
    }

    public function testMethodGetFullNameReturnValue(): void
    {
        $billing = $this->createValidBilling();
        $result = method_exists($billing, 'getFullName');
        $this->assertTrue($result);
        $result = ($billing->getFullName() ?? '');
        $this->assertStringContainsString(($billing->getFirstName() ?? ''), $result);
        $this->assertStringContainsString(($billing->getLastName() ?? ''), $result);
    }

    public function testMethodGetFullAddressReturnValue(): void
    {
        $billing = $this->createValidBilling();
        $result = method_exists($billing, 'getFullAddress');
        $this->assertTrue($result);
        $result = ($billing->getFullAddress() ?? '');
        $this->assertStringContainsString(($billing->getAddress() ?? ''), $result);
        $this->assertStringContainsString(($billing->getCity() ?? ''), $result);
        $this->assertStringContainsString(($billing->getPostcode() ?? ''), $result);
        $this->assertStringContainsString(($billing->getCountry() ?? ''), $result);
    }

    public function testConstantTypeDefaultCommissionRate(): void
    {
        $billing = $this->createValidBilling();
        $className = get_class($billing);
        $result = defined($className . '::DEFAULT_COMMISSION_RATE');
        $this->assertTrue($result);
        $this->assertIsFloat($billing::DEFAULT_COMMISSION_RATE);
    }

    public function testConstantTypeDefaultCurrencyCode(): void
    {
        $billing = $this->createValidBilling();
        $className = get_class($billing);
        $result = defined($className . '::DEFAULT_CURRENCY_CODE');
        $this->assertTrue($result);
        $this->assertIsString($billing::DEFAULT_CURRENCY_CODE);
    }

    public function testConstantTypeDefaultCurrencySymbol(): void
    {
        $billing = $this->createValidBilling();
        $className = get_class($billing);
        $result = defined($className . '::DEFAULT_CURRENCY_SYMBOL');
        $this->assertTrue($result);
        $this->assertIsString($billing::DEFAULT_CURRENCY_SYMBOL);
    }

    public function testMethodConvertToCurrencyUnitReturnValue(): void
    {
        $billing = $this->createValidBilling();
        $result = method_exists($billing, 'convertToCurrencyUnit');
        $this->assertTrue($result);
        $result = $billing->convertToCurrencyUnit(1500);
        $this->assertIsFloat($result);
        //$this->assertSame(15.0, $result);
    }

    public function testMethodConvertToCommissionRateReturnValue(): void
    {
        $billing = $this->createValidBilling();
        $result = method_exists($billing, 'convertToCommissionRate');
        $this->assertTrue($result);
        $result = $billing->convertToCommissionRate(75000);
        $this->assertIsFloat($result);
        //$this->assertSame(7.5, $result);
    }

    public function testMethodConvertCurrencyUnitToStoredDataReturnValue(): void
    {
        $billing = $this->createValidBilling();
        $result = method_exists($billing, 'convertCurrencyUnitToStoredData');
        $this->assertTrue($result);
        $result = $billing->convertCurrencyUnitToStoredData(15.0);
        $this->assertIsInt($result);
        //$this->assertSame(1500, $result);
    }

    public function testMethodConvertCommissionRateToStoredDataReturnValue(): void
    {
        $billing = $this->createValidBilling();
        $result = method_exists($billing, 'convertCommissionRateToStoredData');
        $this->assertTrue($result);
        $result = $billing->convertCommissionRateToStoredData(7.5);
        $this->assertIsInt($result);
        //$this->assertSame(75000, $result);
    }
}
