<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Competition;
use App\Entity\Run;
use App\Entity\Sport;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Competition
 */
final class CompetitionTest extends KernelTestCase
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

    private function createTeamObject(string $country = "FR"): Team
    {
        $team =  new Team();
        $team
            ->setName("RC Strasbourg Alsace")
            ->setCountry($country);
        return $team;
    }

    private function createRunObject(\DateTimeImmutable $date = null): Run
    {
        $run = new Run();
        $startDate = $date ?? new \DateTimeImmutable('+1 day', new \DateTimeZone('UTC'));
        $run
            ->setName('run name')
            ->setEvent('event name')
            ->setStartDate($startDate);
        return $run;
    }

    private function createSportObject(string $country = "FR"): Sport
    {
        $sport =  new Sport();
        $sport
            ->setName("Football")
            ->setMaxMembersByTeam(11)
            ->setMaxTeams(2)
            ->setCountry($country)
            ->setRunType("fixture")
            ->setIndividualType(false)
            ->setCollectiveType(true);
        return $sport;
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

    public function testAddWinnerCompatible()
    {
        $competition = $this->createValidCompetition();
        $team = $this->createTeamObject();
        $competition->addWinner($team);
        $this->assertContains($team, $competition->getWinners());
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
    }

    public function testAddWinnerUncompatible()
    {
        $competition = $this->createValidCompetition();
        $team = $this->createTeamObject('XD');
        $competition->addWinner($team);
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
    }

    public function testAddWinnerCompatibleOverLimit()
    {
        $competition = $this->createValidCompetition();
        $team = $this->createTeamObject();
        $competition->addWinner($team);
        $competition->addWinner($this->createTeamObject('DE'));
        $competition->addWinner($this->createTeamObject('GB'));
        $competition->addWinner($this->createTeamObject('US'));
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
    }

    public function testRemoveWinnerUncompatible(): void
    {
        $competition = $this->createValidCompetition();
        $team = $this->createTeamObject('XD');
        $competition->addWinner($team);
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
        $competition->removeWinner($team);
        $this->assertNotContains($team, $competition->getWinners());
    }

    public function testRemoveWinnerCompatible(): void
    {
        $competition = $this->createValidCompetition();
        $team = $this->createTeamObject();
        $competition->addWinner($team);
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
        $competition->removeWinner($team);
        $this->assertNotContains($team, $competition->getWinners());
    }

    public function testAddRunCompatible()
    {
        $competition = $this->createValidCompetition();
        $run = $this->createRunObject();
        $competition->addRun($run);
        $this->assertCount(1, $competition->getRuns());
        $this->assertContains($run, $competition->getRuns());
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
    }

    public function testAddRunUncompatible()
    {
        $competition = $this->createValidCompetition();
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $run = $this->createRunObject($date);
        $competition->addRun($run);
        $this->assertCount(1, $competition->getRuns());
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
    }

    public function testAddRunCompatibleOverLimit()
    {
        $competition = $this->createValidCompetition();
        $run = $this->createRunObject();
        $competition->setMaxRuns(3);
        $competition->addRun($run);
        $competition->addRun($this->createRunObject());
        $competition->addRun($this->createRunObject());
        $competition->addRun($this->createRunObject());
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
        $this->assertCount(3, $competition->getRuns());
    }

    public function testRemoveRunUncompatible(): void
    {
        $competition = $this->createValidCompetition();
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $run = $this->createRunObject($date);
        $competition->addRun($run);
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
        $competition->removeRun($run);
        $this->assertNotContains($run, $competition->getRuns());
    }

    public function testRemoveRunCompatible(): void
    {
        $competition = $this->createValidCompetition();
        $run = $this->createRunObject();
        $competition->addRun($run);
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
        $competition->removeRun($run);
        $this->assertNotContains($run, $competition->getRuns());
    }

    public function testSportCompatible()
    {
        $competition = $this->createValidCompetition();
        $sport = $this->createSportObject();
        $competition->setSport($sport);
        $this->assertSame($sport, $competition->getSport());
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
    }

    public function testSportUncompatible()
    {
        $competition = $this->createValidCompetition();
        $sport = $this->createSportObject('XD');
        $competition->setSport($sport);
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
    }
}
