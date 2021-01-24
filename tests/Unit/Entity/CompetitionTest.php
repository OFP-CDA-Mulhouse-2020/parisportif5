<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\DataConverter\DateTimeStorageDataConverter;
use App\Entity\BetCategory;
use App\Entity\Competition;
use App\Entity\Member;
use App\Entity\Result;
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

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidCompetition(): Competition
    {
        $converter = new DateTimeStorageDataConverter();
        $competition = new Competition($converter);
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $competition
            ->setDateTimeConverter($converter)
            ->setName('Championnat inter-club')
            ->setStartDate($date->setTime(23, 59, 59, 1000000))
            ->setCountry('FR')
            ->setMaxRuns(2)
            ->setMinRuns(1)
            ->setSport($this->createSportObject())
            ->addBetCategory($this->createBetCategoryObject())
            ->addRun($this->createRunObject($competition));
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
            ->setCountry($country)
            ->setSport($this->createSportObject())
            ->addMember($this->createMemberObject())
            ->setOdds(20000);
        return $team;
    }

    private function createRunObject(Competition $competition, \DateTimeImmutable $date = null): Run
    {
        $converter = new DateTimeStorageDataConverter();
        $run = new Run($converter);
        $startDate = $date ?? new \DateTimeImmutable('+1 day', new \DateTimeZone('UTC'));
        $run
            ->setDateTimeConverter($converter)
            ->setName('run name')
            ->setEvent('event name')
            ->setStartDate($startDate)
            ->setCompetition($competition)
            ->addTeam($this->createTeamObject());
        return $run;
    }

    private function createMemberObject(string $lastName = "Poirot"): Member
    {
        $member = new Member();
        $member
            ->setLastName($lastName)
            ->setFirstName("Jean-Pierre")
            ->setCountry("FR")
            ->setOdds(20000);
        return $member;
    }

    private function createSportObject(string $country = "DE"): Sport
    {
        $sport =  new Sport();
        $sport
            ->setName("Football")
            ->setMaxMembersByTeam(2)
            ->setMinMembersByTeam(1)
            ->setMaxTeamsByRun(2)
            ->setMinTeamsByRun(1)
            ->setCountry($country)
            ->setRunType("fixture")
            ->setIndividualType(false)
            ->setCollectiveType(true);
        return $sport;
    }

    private function createResultObject(Competition $competition, int $value = 0): Result
    {
        $result = new Result();
        $result
            ->setType("time")
            ->setValue($value)
            ->setWinner(false)
            ->setBetCategory($this->createBetCategoryObject())
            ->setCompetition($competition)
            ->setRun(null)
            ->setTeam($this->createTeamObject())
            ->setTeamMember(null);
        return $result;
    }

    public function createBetCategoryObject(string $name = "resultw"): BetCategory
    {
        $betCategory = new BetCategory();
        $betCategory
            ->setName($name)
            ->setAllowDraw(false)
            ->setTarget("teams");
        return $betCategory;
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
        $maxRuns2 = null;
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
        $maxRuns1 = -1;
        $maxRuns2 = 0;
        $competition = $this->createValidCompetition();
        $competition->setMaxRuns($maxRuns1);
        $violations = $this->validator->validate($competition);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $competition->setMaxRuns($maxRuns2);
        $violations = $this->validator->validate($competition);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testMinRunsCompatible()
    {
        $minRuns1 = 0;
        $minRuns2 = 1;
        $competition = $this->createValidCompetition();
        $competition->setMinRuns($minRuns1);
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
        $competition->setMinRuns($minRuns2);
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
    }

    public function testMinRunsUncompatible()
    {
        $minRuns = -1;
        $competition = $this->createValidCompetition();
        $competition->setMinRuns($minRuns);
        $violations = $this->validator->validate($competition);
        $this->assertGreaterThanOrEqual(1, count($violations));
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

    public function testAddRunCompatible()
    {
        $competition = $this->createValidCompetition();
        $run = $this->createRunObject($competition);
        $competition->addRun($run);
        $competition->addRun($this->createRunObject($competition));
        $this->assertCount(2, $competition->getRuns());
        $this->assertContains($run, $competition->getRuns());
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
    }

    public function testAddRunUncompatible()
    {
        $competition = $this->createValidCompetition();
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $run = $this->createRunObject($competition, $date);
        $competition->addRun($run);
        $this->assertCount(2, $competition->getRuns());
        $violations = $this->validator->validate($competition);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testAddRunCompatibleOverLimit()
    {
        $competition = $this->createValidCompetition();
        $run = $this->createRunObject($competition);
        $competition->setMaxRuns(3);
        $competition->addRun($run);
        $competition->addRun($this->createRunObject($competition));
        $competition->addRun($this->createRunObject($competition));
        $competition->addRun($this->createRunObject($competition));
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
        $this->assertCount(3, $competition->getRuns());
    }

    public function testRemoveRunUncompatible(): void
    {
        $competition = $this->createValidCompetition();
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $run = $this->createRunObject($competition, $date);
        $competition->addRun($run);
        $violations = $this->validator->validate($competition);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $competition->removeRun($run);
        $this->assertNotContains($run, $competition->getRuns());
    }

    public function testRemoveRunCompatible(): void
    {
        $competition = $this->createValidCompetition();
        $run = $this->createRunObject($competition);
        $competition->addRun($run);
        $competition->addRun($this->createRunObject($competition));
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

    public function testResultCompatible()
    {
        $competition = $this->createValidCompetition();
        $result = $this->createResultObject($competition);
        $competition->setResult($result);
        $this->assertSame($result, $competition->getResult());
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
    }

    public function testResultUncompatible()
    {
        $competition = $this->createValidCompetition();
        $result = $this->createResultObject($competition, -1);
        $competition->setResult($result);
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
    }

    public function testValidBetCategoryUncompatible()
    {
        $competition = $this->createValidCompetition();
        $betCategory = $this->createBetCategoryObject("result-");
        $competition->addBetCategory($betCategory);
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
    }

    public function testMinimumBetCategoryUncompatible()
    {
        $competition = $this->createValidCompetition();
        $betCategory = $competition->getBetCategories()->get(0);
        $competition->removeBetCategory($betCategory);
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
    }

    public function testBetCategoryCompatible()
    {
        $competition = $this->createValidCompetition();
        $betCategory = $this->createBetCategoryObject();
        $competition->addBetCategory($betCategory);
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
    }
}
