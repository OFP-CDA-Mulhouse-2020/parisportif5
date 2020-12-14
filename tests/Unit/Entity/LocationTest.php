<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Location;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LocationTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    //throw new \Exception($violations);
    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidLocation(): Location
    {
        $location = new Location();
        $location
            ->setPlace('Stade de France 75000 Paris')
            ->setTimeZone('UTC')
            ->setCountry('FR');
        return $location;
    }

    public function testPlacePropertyUncompatible()
    {
        $place1 = '';
        $place2 = '    ';
        $location = $this->createValidLocation();
        $location->setPlace($place1);
        $violations = $this->validator->validate($location);
        $this->assertCount(1, $violations);
        $location->setPlace($place2);
        $violations = $this->validator->validate($location);
        $this->assertCount(1, $violations);
    }

    public function testPlacePropertyCompatible()
    {
        $place = 'Stade Los Santos de Las Vegas';
        $location = $this->createValidLocation();
        $location->setPlace($place);
        $violations = $this->validator->validate($location);
        $this->assertCount(0, $violations);
    }

     /**
     * @dataProvider timeZonePropertyUnconformityProvider
     */
    public function testTimeZonePropertyUnconformity(string $timeZone): void
    {
        $location = $this->createValidLocation();
        $location->setTimeZone($timeZone);
        $violations = $this->validator->validate($location);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function timeZonePropertyUnconformityProvider(): array
    {
        return [
            ['Antartica/Inconnu'],
            ['Europe_Paris'],
            ['europe/Paris'],
            [''],
            ['   ']
        ];
    }

    /**
     * @dataProvider timeZonePropertyConformityProvider
     */
    public function testTimeZonePropertyConformity(string $timeZone): void
    {
        $location = $this->createValidLocation();
        $location->setTimeZone($timeZone);
        $violations = $this->validator->validate($location);
        $this->assertCount(0, $violations);
    }

    public function timeZonePropertyConformityProvider(): array
    {
        return [
            ['Antarctica/McMurdo'],
            ['Europe/Paris'],
            ['Africa/Johannesburg']
        ];
    }

    /**
     * @dataProvider countryPropertyUnconformityProvider
     */
    public function testCountryPropertyUnconformity(string $country)
    {
        $location = $this->createValidLocation();
        $location->setCountry($country);
        $violations = $this->validator->validate($location);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function countryPropertyUnconformityProvider(): array
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
     * @dataProvider countryPropertyConformityProvider
     * ISO 3166-1 alpha-2 => 2 lettres majuscules
     */
    public function testCountryPropertyConformity(string $country)
    {
        $location = $this->createValidLocation();
        $location->setCountry($country);
        $violations = $this->validator->validate($location);
        $this->assertCount(0, $violations);
    }

    public function countryPropertyConformityProvider(): array
    {
        return [
            ["FR"],
            ["DE"]
        ];
    }
}
