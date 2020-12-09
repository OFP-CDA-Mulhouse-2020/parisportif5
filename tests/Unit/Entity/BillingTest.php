<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Billing;
use App\Entity\Exception\BillingInvalidNameException;
use App\Entity\Exception\BillingBlankNameException;
use App\Entity\Exception\BillingInvalidAddressException;
use App\Entity\Exception\BillingNameLengthException;
use PHPUnit\Framework\TestCase;

class BillingTest extends TestCase
{
    private function billingInitialization(): Billing
    {
        $billing = new Billing();
        return $billing;
    }

    /**
     * @dataProvider nameCompatibleProvider
     */
    public function testNameCompatible(string $name)
    {
        $billing = $this->billingInitialization();
        $this->assertStringMatchesFormat('%s', $name);
        $billing->setFirstName($name);
        $billing->setLastName($name);
    }

    public function nameCompatibleProvider(): array
    {
        return [
            ["Anaïs"],
            ["édouârd"],
            ["Jean-Marc de l'Atour"],
            ["ggg"]
        ];
    }

    /**
     * @dataProvider nameUncompatibleProvider
     */
    public function testNameUncompatibleException(string $name)
    {
        $billing = $this->billingInitialization();
        $this->expectException(BillingInvalidNameException::class);
        $billing->setFirstName($name);
        $this->expectException(BillingInvalidNameException::class);
        $billing->setLastName($name);
    }

    public function nameUncompatibleProvider(): array
    {
        return [
            ["gabin#"],
            ["fabien25"]
        ];
    }

    public function testNameUncompatibleBlankException()
    {
        $name1 = '';
        $name2 = '  ';
        $billing = $this->billingInitialization();
        $this->expectException(BillingBlankNameException::class);
        $billing->setFirstName($name1);
        $this->expectException(BillingBlankNameException::class);
        $billing->setLastName($name1);
        $this->expectException(BillingBlankNameException::class);
        $billing->setFirstName($name2);
        $this->expectException(BillingBlankNameException::class);
        $billing->setLastName($name2);
    }

    /**
     * @dataProvider nameUncompatibleLengthProvider
     */
    public function testNameUncompatibleLengthException(string $name)
    {
        $billing = $this->billingInitialization();
        $this->expectException(BillingNameLengthException::class);
        $billing->setFirstName($name);
        $this->expectException(BillingNameLengthException::class);
        $billing->setLastName($name);
    }

    public function nameUncompatibleLengthProvider(): array
    {
        return [
            ["g"],
            ["monsieurdontlenomestbientroplong"]
        ];
    }

    public function testAddressUncompatibleBlankException()
    {
        $name1 = '';
        $name2 = '  ';
        $billing = $this->billingInitialization();
        $this->expectException(BillingBlankAddressException::class);
        $billing->setAddress($name1);
        $this->expectException(BillingBlankAddressException::class);
        $billing->setPostcode($name1);
        $this->expectException(BillingBlankAddressException::class);
        $billing->setCity($name1);
        $this->expectException(BillingBlankAddressException::class);
        $billing->setCountry($name1);
        $this->expectException(BillingBlankAddressException::class);
        $billing->setAddress($name2);
        $this->expectException(BillingBlankAddressException::class);
        $billing->setPostcode($name2);
        $this->expectException(BillingBlankAddressException::class);
        $billing->setCity($name2);
        $this->expectException(BillingBlankAddressException::class);
        $billing->setCountry($name2);
    }

    /**
     * @dataProvider addressUncompatibleProvider
     */
    public function testAddressUncompatibleException(string $address)
    {
        $billing = $this->billingInitialization();
        $this->expectException(BillingInvalidAddressException::class);
        $billing->setAddress($address);
    }

    public function addressUncompatibleProvider(): array
    {
        return [
            ["5 rue Jean-Gabin#"],
            ["lieu-dit Lefabien@"]
        ];
    }

    /**
     * @dataProvider cityUncompatibleProvider
     */
    public function testCityUncompatibleException(string $city)
    {
        $billing = $this->billingInitialization();
        $this->expectException(BillingInvalidAddressException::class);
        $billing->setCity($city);
    }

    public function cityUncompatibleProvider(): array
    {
        return [
            ["Villefranche1"],
            ["Pais&"]
        ];
    }
}
