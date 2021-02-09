<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Service\DateTimeStorageDataConverter;
use App\Entity\Billing;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Billing
 */
final class BillingTest extends WebTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidBilling(): Billing
    {
        $converter = new DateTimeStorageDataConverter();
        $billing = new Billing($converter);
        $billing
            ->setDateTimeConverter($converter)
            ->setFirstName("Dupont")
            ->setLastName("Tintin")
            ->setAddress("1 avenue st martin")
            ->setCity("Colmar")
            ->setPostcode("68000")
            ->setCountry("FR")
            ->setDesignation("paris n1")
            ->setOrderNumber(1)
            ->setInvoiceNumber(1)
            ->setAmount(5)
            ->setOperationType("debit");
        return $billing;
    }

    private function createUserObject(string $country = "FR"): User
    {
        $converter = new DateTimeStorageDataConverter();
        $user = new User($converter);
        $user
            ->setDateTimeConverter($converter)
            ->setCivility("Monsieur")
            ->setFirstName("Tintin")
            ->setLastName("Dupont")
            ->setBillingAddress("1 avenue st martin")
            ->setBillingCity("Colmar")
            ->setBillingPostcode("68000")
            ->setBillingCountry($country)
            ->setBirthDate(new \DateTimeImmutable("2000-10-10"))
            ->setPlainPassword("Azerty78")
            ->setPassword("hashpassword")
            ->setEmail("haddock@gmail.fr")
            ->setTimeZoneSelected("Europe/Paris")
            ->setResidenceProof("identity_card.pdf")
            ->setIdentityDocument("invoice.jpg");
        return $user;
    }

    /**
     * @dataProvider firstNameCompatibleProvider
     */
    public function testFirstNameCompatible(string $name): void
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
            ["ab"],
            ["nomquiestbeaucouptroplong"]
        ];
    }

    /**
     * @dataProvider lastNameCompatibleProvider
     */
    public function testLastNameCompatible(string $name): void
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
            ["ab"],
            ["nomquiestbeaucouptroplong"]
        ];
    }

    /**
     * @dataProvider lastNameUncompatibleProvider
     */
    public function testLastNameUncompatible(string $name): void
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
            ["a"],
            ["nomquiestbeaucouptroplongg"]
        ];
    }

    /**
     * @dataProvider firstNameUncompatibleProvider
     */
    public function testFirstNameUncompatible(string $name): void
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
            ["a"],
            ["pnomquiestbeaucouptroplong"]
        ];
    }

    /**
     * @dataProvider addressCompatibleProvider
     */
    public function testAddressCompatible(string $address): void
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
    public function testAddressUncompatible(string $address): void
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
    public function testCityCompatible(string $city): void
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
    public function testCityUncompatible(string $city): void
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
    public function testPostcodeCompatible(string $postcode): void
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
    public function testPostcodeUncompatible(string $postcode): void
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
    public function testCountryCompatible(string $country): void
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
    public function testCountryUncompatible(string $country): void
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

    public function testOperationTypeCompatible(): void
    {
        $operationType1 = 'credit';
        $operationType2 = 'debit';
        $billing = $this->createValidBilling();
        $billing->setOperationType($operationType1);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
        $billing->setOperationType($operationType2);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider operationTypeUncompatibleProvider
     */
    public function testOperationTypeUncompatible(string $operationType): void
    {
        $billing = $this->createValidBilling();
        $billing->setOperationType('credit');
        $billing->setOperationType($operationType);
        $this->assertSame('credit', $billing->getOperationType());
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
    }

    public function operationTypeUncompatibleProvider(): array
    {
        return [
            [' '],
            [''],
            ['chouette'],
            ["débit"],
            ["crédit"]
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

    public function testDesignationUncompatible(): void
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

    public function testOrderNumberCompatible(): void
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

    public function testOrderNumberUncompatible(): void
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

    public function testInvoiceNumberCompatible(): void
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

    public function testInvoiceNumberUncompatible(): void
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

    public function testAmountCompatible(): void
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

    public function testAmountUncompatible(): void
    {
        $amount = -1;
        $billing = $this->createValidBilling();
        $billing->setAmount($amount);
        $violations = $this->validator->validate($billing);
        $this->assertCount(1, $violations);
    }

    public function testCommissionRateCompatible(): void
    {
        $commissionRate1 = '0.0000';
        $commissionRate2 = '999999.9999';
        $billing = $this->createValidBilling();
        $billing->setCommissionRate($commissionRate1);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
        $billing->setCommissionRate($commissionRate2);
        $violations = $this->validator->validate($billing);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider commissionRateUncompatibleProvider
     */
    public function tesCommissionRateUncompatible(string $commissionRate): void
    {
        $billing = $this->createValidBilling();
        $billing->setCommissionRate($commissionRate);
        $violations = $this->validator->validate($billing);
        $this->assertCount(1, $violations);
    }

    public function commissionRateUncompatibleProvider(): array
    {
        return [
            ['-1'],
            ['1000000'],
            ['string']
        ];
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

    public function testUserUncompatible(): void
    {
        $billing = $this->createValidBilling();
        $user = $this->createUserObject('XD');
        $billing->setUser($user);
        $violations = $this->validator->validate($billing, null, ['registration', 'login', 'profile', 'password_update', 'identifier_update', 'parameter']);
        $this->assertCount(1, $violations);
    }

    public function testUserCompatible(): void
    {
        $billing = $this->createValidBilling();
        $user = $this->createUserObject();
        $billing->setUser($user);
        $this->assertSame($user, $billing->getUser());
        $violations = $this->validator->validate($billing, null, ['registration', 'login', 'profile', 'password_update', 'identifier_update', 'parameter']);
        $this->assertCount(0, $violations);
    }

    public function testMethodHasUser(): void
    {
        $billing = $this->createValidBilling();
        $method = method_exists($billing, 'hasUser');
        $this->assertTrue($method);
        $result = $billing->hasUser();
        $this->assertIsBool($result);
    }
}
