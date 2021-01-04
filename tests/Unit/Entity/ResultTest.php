<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

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
 * @covers \Result
 */
final class ResultTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidResult(): Result
    {
        $result = new Result();
        $result
            ->setType("time")
            ->setValue(0)
            ->setWinner(false)
            ->setBetCategory($this->createBetCategoryObject())
            ->setCompetition($this->createCompetitionObject())
            ->setTeam($this->createTeamObject());
        return $result;
    }

    public function createBetCategoryObject(string $name = "result"): BetCategory
    {
        $betCategory = new BetCategory();
        $betCategory->setName($name);
        return $betCategory;
    }

    private function createCompetitionObject(string $country = "FR"): Competition
    {
        $competition = new Competition();
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $competition
            ->setName('name')
            ->setStartDate($date->setTime(23, 59, 59, 1000000))
            ->setCountry($country)
            ->setMaxRuns(1)
            ->setSport($this->createSportObject())
            ->addBetCategory($this->createBetCategoryObject());
        return $competition;
    }

    private function createSportObject(string $country = "FR"): Sport
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

    private function createRunObject(\DateTimeImmutable $date = null): Run
    {
        $run = new Run();
        $startDate = $date ?? new \DateTimeImmutable('+1 day', new \DateTimeZone('UTC'));
        $run
            ->setName('run name')
            ->setEvent('event name')
            ->setStartDate($startDate)
            ->setCompetition($this->createCompetitionObject())
            ->addTeam($this->createTeamObject());
        return $run;
    }

    private function createTeamObject(string $country = "FR"): Team
    {
        $team =  new Team();
        $team
            ->setName("RC Strasbourg Alsace")
            ->setCountry($country)
            ->setSport($this->createSportObject())
            ->addMember($this->createMemberObject());
        return $team;
    }

    private function createMemberObject(string $lastName = "Poirot"): Member
    {
        $member = new Member();
        $member
            ->setLastName($lastName)
            ->setFirstName("Jean-Pierre")
            ->setCountry("FR");
        return $member;
    }

    public function testResultTypeCompatible(): void
    {
        $resultType1 = "time";
        $resultType2 = "point";
        $result = $this->createValidResult();
        $result->setType($resultType1);
        $violations = $this->validator->validate($result);
        $this->assertCount(0, $violations);
        $result->setType($resultType2);
        $violations = $this->validator->validate($result);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider resultTypeUncompatibleProvider
     */
    public function testResultTypeUncompatible(string $resultType): void
    {
        $result = $this->createValidResult();
        $result->setType($resultType);
        $violations = $this->validator->validate($result);
        $this->assertCount(1, $violations);
    }

    public function resultTypeUncompatibleProvider(): array
    {
        return [
            ["GOAL"],
            ["TIME"],
            ["POINT"],
            ["score"],
            [''],
            ['   ']
        ];
    }

    public function testResultValueUncompatible()
    {
        $value1 = -15;
        $value2 = -1;
        $result = $this->createValidResult();
        $result->setValue($value1);
        $violations = $this->validator->validate($result);
        $this->assertCount(1, $violations);
        $result->setValue($value2);
        $violations = $this->validator->validate($result);
        $this->assertCount(1, $violations);
    }

    public function testResultValueCompatible()
    {
        $value1 = 0;
        $value2 = 1000000000;
        $result = $this->createValidResult();
        $result->setValue($value1);
        $violations = $this->validator->validate($result);
        $this->assertCount(0, $violations);
        $result->setValue($value2);
        $violations = $this->validator->validate($result);
        $this->assertCount(0, $violations);
    }

    public function testCompetitionUncompatible(): void
    {
        $result = $this->createValidResult();
        $competition = $this->createCompetitionObject('XD');
        $result->setCompetition($competition);
        $violations = $this->validator->validate($result);
        $this->assertCount(1, $violations);
    }

    public function testCompetitionCompatible(): void
    {
        $result = $this->createValidResult();
        $competition = $this->createCompetitionObject();
        $result->setCompetition($competition);
        $this->assertSame($competition, $result->getCompetition());
        $violations = $this->validator->validate($result);
        $this->assertCount(0, $violations);
    }

    public function testRunUncompatible(): void
    {
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $result = $this->createValidResult();
        $run = $this->createRunObject($date);
        $result->setRun($run);
        $violations = $this->validator->validate($result);
        $this->assertCount(1, $violations);
    }

    public function testRunCompatible(): void
    {
        $result = $this->createValidResult();
        $run = $this->createRunObject();
        $result->setRun($run);
        $this->assertSame($run, $result->getRun());
        $violations = $this->validator->validate($result);
        $this->assertCount(0, $violations);
    }

    public function testTeamUncompatible(): void
    {
        $result = $this->createValidResult();
        $team = $this->createTeamObject('XD');
        $result->setTeam($team);
        $violations = $this->validator->validate($result);
        $this->assertCount(1, $violations);
    }

    public function testTeamCompatible(): void
    {
        $result = $this->createValidResult();
        $team = $this->createTeamObject();
        $result->setTeam($team);
        $this->assertSame($team, $result->getTeam());
        $violations = $this->validator->validate($result);
        $this->assertCount(0, $violations);
    }

    public function testTeamMemberUncompatible(): void
    {
        $result = $this->createValidResult();
        $member = $this->createMemberObject('SPARRO\/\/');
        $result->setTeamMember($member);
        $violations = $this->validator->validate($result);
        $this->assertCount(1, $violations);
    }

    public function testTeamMemberCompatible(): void
    {
        $result = $this->createValidResult();
        $member = $this->createMemberObject();
        $result->setTeamMember($member);
        $this->assertSame($member, $result->getTeamMember());
        $violations = $this->validator->validate($result);
        $this->assertCount(0, $violations);
    }

    public function testBetCategoryUncompatible(): void
    {
        $result = $this->createValidResult();
        $betCategory = $this->createBetCategoryObject("result-");
        $result->setBetCategory($betCategory);
        $violations = $this->validator->validate($result);
        $this->assertCount(1, $violations);
    }

    public function testBetCategoryCompatible(): void
    {
        $result = $this->createValidResult();
        $betCategory = $this->createBetCategoryObject();
        $result->setBetCategory($betCategory);
        $this->assertSame($betCategory, $result->getBetCategory());
        $violations = $this->validator->validate($result);
        $this->assertCount(0, $violations);
    }

    public function testResultTargetUncompatible(): void
    {
        $result = $this->createValidResult();
        $result->setTeam(null);
        $result->setTeamMember(null);
        $violations = $this->validator->validate($result);
        $this->assertCount(1, $violations);
    }

    public function testResultTargetCompatible(): void
    {
        $result = $this->createValidResult();
        $teamMember = $this->createMemberObject();
        $result->setTeam(null);
        $result->setTeamMember($teamMember);
        $this->assertSame($teamMember, $result->getTeamMember());
        $violations = $this->validator->validate($result);
        $this->assertCount(0, $violations);
    }
}
