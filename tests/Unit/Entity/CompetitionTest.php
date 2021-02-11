<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\BetCategory;
use App\Entity\Competition;
use App\Entity\Member;
use App\Entity\Run;
use App\Entity\Sport;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Competition
 */
final class CompetitionTest extends WebTestCase
{
    private ValidatorInterface $validator;

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
            ->setOdds('2');
        return $team;
    }

    private function createRunObject(Competition $competition, \DateTimeImmutable $date = null): Run
    {
        $run = new Run();
        $startDate = $date ?? new \DateTimeImmutable('+1 day', new \DateTimeZone('UTC'));
        $run
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
            ->setOdds('2');
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

    public function createBetCategoryObject(string $name = "resultw"): BetCategory
    {
        $betCategory = new BetCategory();
        $betCategory
            ->setName($name)
            ->setAllowDraw(false)
            ->setTarget("teams")
            ->setOnCompetition(false);
        return $betCategory;
    }

    /**
     * @dataProvider namePropertyCompatibleProvider
     */
    public function testNamePropertyCompatible(string $competitionName): void
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

    public function testNamePropertyUncompatible(): void
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
        $timeZone = $this->createDefaultTimeZone();
        $startDate = new \DateTimeImmutable('now', $timeZone);
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
        $timeZone = $this->createDefaultTimeZone();
        $startDate = new \DateTimeImmutable('now', $timeZone);
        return [
            [$startDate->modify("+1 day")],
            [$startDate->modify("+1 month")]
        ];
    }

    /**
     * @dataProvider countryCompatibleProvider
     * ISO 3166-1 alpha-2 => 2 lettres majuscules
     */
    public function testCountryCompatible(string $country): void
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
    public function testCountryUncompatible(string $country): void
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

    public function testMaxRunsCompatible(): void
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

    public function testMaxRunsUncompatible(): void
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

    public function testMinRunsCompatible(): void
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

    public function testMinRunsUncompatible(): void
    {
        $minRuns = -1;
        $competition = $this->createValidCompetition();
        $competition->setMinRuns($minRuns);
        $violations = $this->validator->validate($competition);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testAddRunCompatible(): void
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

    public function testAddRunUncompatible(): void
    {
        $competition = $this->createValidCompetition();
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $run = $this->createRunObject($competition, $date);
        $competition->addRun($run);
        $this->assertCount(2, $competition->getRuns());
        $violations = $this->validator->validate($competition);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testAddRunCompatibleOverLimit(): void
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

    public function testSportCompatible(): void
    {
        $competition = $this->createValidCompetition();
        $sport = $this->createSportObject();
        $competition->setSport($sport);
        $this->assertSame($sport, $competition->getSport());
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
    }

    public function testSportUncompatible(): void
    {
        $competition = $this->createValidCompetition();
        $sport = $this->createSportObject('XD');
        $competition->setSport($sport);
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
    }

    public function testValidBetCategoryUncompatible(): void
    {
        $competition = $this->createValidCompetition();
        $betCategory = $this->createBetCategoryObject("result-");
        $competition->addBetCategory($betCategory);
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
    }

    public function testMinimumBetCategoryUncompatible(): void
    {
        $competition = $this->createValidCompetition();
        $betCategory = $competition->getBetCategories()->get(0);
        $competition->removeBetCategory($betCategory);
        $violations = $this->validator->validate($competition);
        $this->assertCount(1, $violations);
    }

    public function testBetCategoryCompatible(): void
    {
        $competition = $this->createValidCompetition();
        $betCategory = $this->createBetCategoryObject();
        $competition->addBetCategory($betCategory);
        $violations = $this->validator->validate($competition);
        $this->assertCount(0, $violations);
    }
}
