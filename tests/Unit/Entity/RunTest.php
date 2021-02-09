<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Service\DateTimeStorageDataConverter;
use App\Entity\BetCategory;
use App\Entity\Competition;
use App\Entity\Location;
use App\Entity\Member;
use App\Entity\Run;
use App\Entity\Sport;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Run
 */
final class RunTest extends WebTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidRun(): Run
    {
        $run = new Run();
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $run
            ->setName('run name')
            ->setEvent('event name')
            ->setStartDate($date->setTime(23, 59, 59, 1000000))
            ->addTeam($this->createTeamObject());
        return $run;
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

    private function createLocationObject(string $country = "FR"): Location
    {
        $location = new Location();
        $location
            ->setPlace('Stade de France 75000 Paris')
            ->setTimeZone('UTC')
            ->setCountry($country);
        return $location;
    }

    private function createCompetitionObject(string $country = "FR"): Competition
    {
        $competition = new Competition();
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $competition
            ->setName('Championnat inter-club')
            ->setStartDate($date->setTime(23, 59, 59, 1000000))
            ->setCountry($country)
            ->setMaxRuns(2)
            ->setMinRuns(0)
            ->setSport($this->createSportObject())
            ->addBetCategory($this->createBetCategoryObject());
        return $competition;
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
    public function testNamePropertyCompatible(string $runName): void
    {
        $run = $this->createValidRun();
        $run->setName($runName);
        $violations = $this->validator->validate($run);
        $this->assertCount(0, $violations);
    }

    public function namePropertyCompatibleProvider(): array
    {
        return [
            ["Spécial n°1 des Vosges"],
            ["Match pool n°1 France-Espagne"]
        ];
    }

    public function testNamePropertyUncompatible(): void
    {
        $runName1 = '';
        $runName2 = '   ';
        $run = $this->createValidRun();
        $run->setName($runName1);
        $violations = $this->validator->validate($run);
        $this->assertCount(1, $violations);
        $run->setName($runName2);
        $violations = $this->validator->validate($run);
        $this->assertCount(1, $violations);
    }

    /**
     * @dataProvider eventPropertyCompatibleProvider
     */
    public function testEventPropertyCompatible(string $event): void
    {
        $run = $this->createValidRun();
        $run->setEvent($event);
        $violations = $this->validator->validate($run);
        $this->assertCount(0, $violations);
    }

    public function eventPropertyCompatibleProvider(): array
    {
        return [
            ["Championnat des Vosges"],
            ["Matchs de pool n°1"]
        ];
    }

    public function testEventPropertyUncompatible(): void
    {
        $event1 = '';
        $event2 = '   ';
        $run = $this->createValidRun();
        $run->setEvent($event1);
        $violations = $this->validator->validate($run);
        $this->assertCount(1, $violations);
        $run->setEvent($event2);
        $violations = $this->validator->validate($run);
        $this->assertCount(1, $violations);
    }

    /**
     * @dataProvider startDateUnconformityProvider
     */
    public function testStartDateUnconformity(\DateTimeInterface $startDate): void
    {
        $run = $this->createValidRun();
        $run->setStartDate($startDate);
        $violations = $this->validator->validate($run);
        $this->assertCount(1, $violations);
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
        $run = $this->createValidRun();
        $run->setStartDate($startDate);
        $violations = $this->validator->validate($run);
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

    public function testLocationCompatible(): void
    {
        $run = $this->createValidRun();
        $location = $this->createLocationObject();
        $run->setLocation($location);
        $this->assertSame($location, $run->getLocation());
        $violations = $this->validator->validate($run);
        $this->assertCount(0, $violations);
    }

    public function testLocationUncompatible(): void
    {
        $run = $this->createValidRun();
        $location = $this->createLocationObject('XD');
        $run->setLocation($location);
        $violations = $this->validator->validate($run);
        $this->assertCount(1, $violations);
    }

    public function testAddTeamCompatible(): void
    {
        $run = $this->createValidRun();
        $competition = $this->createCompetitionObject();
        $sport = $this->createSportObject();
        $competition->setSport($sport);
        $run->setCompetition($competition);
        $team = $this->createTeamObject();
        $run->addTeam($team);
        $this->assertContains($team, $run->getTeams());
        $violations = $this->validator->validate($run);
        $this->assertCount(0, $violations);
    }

    public function testAddTeamUncompatible(): void
    {
        $run = $this->createValidRun();
        $competition = $this->createCompetitionObject();
        $sport = $this->createSportObject();
        $competition->setSport($sport);
        $run->setCompetition($competition);
        $team = $this->createTeamObject('XD');
        $run->addTeam($team);
        $violations = $this->validator->validate($run);
        $this->assertCount(1, $violations);
    }

    public function testAddTeamCompatibleOverLimit(): void
    {
        $run = $this->createValidRun();
        $competition = $this->createCompetitionObject();
        $sport = $this->createSportObject();
        $competition->setSport($sport);
        $run->setCompetition($competition);
        $team = $this->createTeamObject();
        $run->addTeam($team);
        $run->addTeam($this->createTeamObject('DE'));
        $run->addTeam($this->createTeamObject('GB'));
        $run->addTeam($this->createTeamObject('US'));
        $violations = $this->validator->validate($run);
        $this->assertCount(0, $violations);
        $maxTeams = $run->getCompetition()->getSport()->getMaxTeamsByRun();
        $this->assertCount($maxTeams, $run->getTeams());
    }

    public function testRemoveTeamUncompatible(): void
    {
        $run = $this->createValidRun();
        $competition = $this->createCompetitionObject();
        $sport = $this->createSportObject();
        $competition->setSport($sport);
        $run->setCompetition($competition);
        $team = $this->createTeamObject('XD');
        $run->addTeam($team);
        $violations = $this->validator->validate($run);
        $this->assertCount(1, $violations);
        $run->removeTeam($team);
        $this->assertNotContains($team, $run->getTeams());
    }

    public function testRemoveTeamCompatible(): void
    {
        $run = $this->createValidRun();
        $competition = $this->createCompetitionObject();
        $sport = $this->createSportObject();
        $competition->setSport($sport);
        $run->setCompetition($competition);
        $team = $this->createTeamObject();
        $run->addTeam($team);
        $violations = $this->validator->validate($run);
        $this->assertCount(0, $violations);
        $run->removeTeam($team);
        $this->assertNotContains($team, $run->getTeams());
    }
}
