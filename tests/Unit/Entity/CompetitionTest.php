<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Competition;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CompetitionTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    //throw new \Exception($violations);
    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidCompetition(): Competition
    {
        $competition = new Competition();
        $date = new \DateTime('now', new \DateTimeZone('UTC'));
        //throw new \Exception(var_dump($date));
        $competition
            ->setName('name')
            ->setStartDate((new \DateTime('now', new \DateTimeZone('UTC')))->modify('+1 hour'))
            ->setEndDate((new \DateTime('now', new \DateTimeZone('UTC')))->modify('+1 year'))
            ->setCountry('FR');
        return $competition;
    }

    private function createDefaultTimeZone(): \DateTimeZone
    {
        return new \DateTimeZone('UTC');
    }

    /**
     * @dataProvider namePropertyCompatibleProvider
     */
    public function testNamePropertyCompatible(string $competitionName)
    {
        $competition = $this->createValidCompetition();
        $competition->setName($competitionName);
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
    }

    public function namePropertyCompatibleProvider(): array
    {
        return [
            ["Grand prix de France"],
            ["Championnat de France"]
        ];
    }

    public function testNamePropertyUncompatible()
    {
        $competitionName1 = '';
        $competitionName2 = '   ';
        $competition = $this->createValidCompetition();
        $competition->setName($competitionName1);
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
        $competition->setName($competitionName2);
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
    }

    /**
     * @dataProvider startDateUnconformityProvider
     */
    public function testStartDateUnconformity(\DateTimeInterface $startDate): void
    {
        $competition = $this->createValidCompetition();
        $competition->setStartDate($startDate);
        $violations = $this->validator->validate($competition);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function startDateUnconformityProvider(): array
    {
        $timezone = $this->createDefaultTimeZone();
        $startDate = new \DateTime('now', $timezone);
        return [
            [$startDate],
            [$startDate->modify('-1 hour')],
            [$startDate->modify('-1 day')->setTime(23, 59, 59, 999999)],
            [(new \DateTime('now', $timezone))->sub(new \DateInterval('P1Y'))]
        ];
    }

    /**
     * @dataProvider startDateConformityProvider
     */
    public function testStartDateConformity(\DateTimeInterface $startDate): void
    {
        $competition = $this->createValidCompetition();
        $competition->setStartDate($startDate);
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
    }

    public function startDateConformityProvider(): array
    {
        $timezone = $this->createDefaultTimeZone();
        $startDate1 = new \DateTime('now', $timezone);
        $startDate2 = clone $startDate1;
        return [
            [$startDate1->modify("+1 day")],
            [$startDate2->modify("+1 month")]
        ];
    }

    /**
     * @dataProvider endDateUnconformityProvider
     */
    public function testEndDateUnconformity(\DateTimeInterface $endDate): void
    {
        $competition = $this->createValidCompetition();
        $competition->setEndDate($endDate);
        $violations = $this->validator->validate($competition);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function endDateUnconformityProvider(): array
    {
        $timezone = $this->createDefaultTimeZone();
        $endDate = new \DateTime('now', $timezone);
        return [
            [$endDate],
            [$endDate->modify('-1 hour')],
            [$endDate->modify('-1 day')],
            [(new \DateTime('now', $timezone))->sub(new \DateInterval('P1Y'))]
        ];
    }

    /**
     * @dataProvider endDateConformityProvider
     */
    public function testEndDateConformity(\DateTimeInterface $endDate): void
    {
        $competition = $this->createValidCompetition();
        $competition->setEndDate($endDate);
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
    }

    public function endDateConformityProvider(): array
    {
        $timezone = $this->createDefaultTimeZone();
        $endDate1 = new \DateTime('now', $timezone);
        $endDate2 = clone $endDate1;
        return [
            [$endDate1->modify("+1 day")->setTime(23, 59, 59, 999999)],
            [$endDate2->modify("+1 month")]
        ];
    }

    /**
     * @dataProvider countryCompatibleProvider
     * ISO 3166-1 alpha-2 => 2 lettres majuscules
     */
    public function testCountryCompatible(string $country)
    {
        $competition = $this->createValidCompetition();
        $competition->setCountry($country);
        $violations = $this->validator->validate($competition);
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
        $competition = $this->createValidCompetition();
        $competition->setCountry($country);
        $violations = $this->validator->validate($competition);
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

    public function testMethodIsFinishReturnFalse()
    {
        $competition = $this->createValidCompetition();
        $exist = method_exists($competition, 'isFinish');
        $this->assertTrue($exist);
        $result = $competition->isFinish();
        $this->assertFalse($result);
    }

    public function testMethodIsOngoingReturnFalse()
    {
        $competition = $this->createValidCompetition();
        $exist = method_exists($competition, 'isOngoing');
        $this->assertTrue($exist);
        $result = $competition->isOngoing();
        $this->assertFalse($result);
    }
}
