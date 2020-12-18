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
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $competition
            ->setName('name')
            ->setStartDate($date->setTime(23, 59, 59, 1000000))
            ->setCountry('FR')
            ->setMaxRuns(1);
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
        $startDate = new \DateTimeImmutable('now', $timezone);
        return [
            [$startDate],
            [$startDate->modify('-1 hour')],
            [$startDate->modify('-1 day')->setTime(23, 59, 59, 999999)],
            [$startDate->modify('-1 year')]
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
        $startDate = new \DateTimeImmutable('now', $timezone);
        return [
            [$startDate->modify("+1 day")],
            [$startDate->modify("+1 month")]
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
        $endDate = new \DateTimeImmutable('now', $timezone);
        return [
            [$endDate->setTime(23, 59, 59, 1000000)],
            [$endDate->modify('-1 hour')],
            [$endDate->modify('-1 day')->setTime(23, 59, 59, 999999)],
            [$endDate->modify('-1 year')]
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
        $endDate = new \DateTimeImmutable('now', $timezone);
        return [
            [$endDate->modify("+1 day")->setTime(23, 59, 59, 999999)],
            [$endDate->modify("+1 month")]
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

    public function testMaxRunsCompatible()
    {
        $maxRuns1 = 1;
        $maxRuns2 = 50;
        $competition = $this->createValidCompetition();
        $competition->setMaxRuns($maxRuns1);
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
        $competition->setMaxRuns($maxRuns2);
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
    }

    public function testMaxRunsUncompatible()
    {
        $maxRuns1 = 0;
        $maxRuns2 = -1;
        $competition = $this->createValidCompetition();
        $competition->setMaxRuns($maxRuns1);
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
        $competition->setMaxRuns($maxRuns2);
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
    }

    public function testMethodIsFinishReturnFalse()
    {
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $competition = $this->createValidCompetition();
        $exist = method_exists($competition, 'isFinish');
        $this->assertTrue($exist);
        $competition->setEndDate($date->modify('+2 day'));
        $result = $competition->isFinish();
        $this->assertFalse($result);
    }

    public function testMethodIsOngoingReturnFalse()
    {
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $competition = $this->createValidCompetition();
        $exist = method_exists($competition, 'isOngoing');
        $this->assertTrue($exist);
        $competition->setEndDate($date->modify('+2 day'));
        $result = $competition->isOngoing();
        $this->assertFalse($result);
    }
}
