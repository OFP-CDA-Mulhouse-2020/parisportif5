<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Member;
use App\Entity\MemberRole;
use App\Entity\MemberStatus;
use App\Entity\Sport;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

//tester si l'equipe est liee au sport
//tester la limite de membres par equipe en fonction du sport

/**
 * @covers \Team
 */
final class TeamTest extends WebTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidTeam(): Team
    {
        $team =  new Team();
        $team->setName("RC Strasbourg Alsace");
        $team->setCountry("FR");
        $team->setSport($this->createSportObject());
        $team->addMember($this->createMemberObject());
        $team->setOdds('2');
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

    private function createSportObject(string $country = "PT"): Sport
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

    public function testOddsCompatible(): void
    {
        $odds1 = '0';
        $odds2 = '10000000';
        $team = $this->createValidTeam();
        $team->setOdds($odds1);
        $violations = $this->validator->validate($team);
        $this->assertCount(0, $violations);
        $team->setOdds($odds2);
        $violations = $this->validator->validate($team);
        $this->assertCount(0, $violations);
    }

    public function testOddsUncompatible(): void
    {
        $odds1 = '-1';
        $odds2 = '100000000';
        $team = $this->createValidTeam();
        $team->setOdds($odds1);
        $violations = $this->validator->validate($team);
        $this->assertCount(1, $violations);
        $team->setOdds($odds2);
        $violations = $this->validator->validate($team);
        $this->assertCount(1, $violations);
    }

    /**
     * @dataProvider validTeamNameProvider
     */
    public function testIfTeamNameIsValid(string $name): void
    {
        $team = $this->createValidTeam();
        $team->setName($name);
        $violations = $this->validator->validate($team);
        $this->assertCount(0, $violations);
    }

    public function validTeamNameProvider(): array
    {
        return [
            ["Paris Saint-Germain Football Club"],
            ["Västerås Hockey"],
            ["Fenerbahçe 1907"],
            ["Székesfehérvár Futball"],
            ["Cartagena 1444"],
            ["A.E.K"],
            ["OLA"],
            ["OL"]
        ];
    }

    /**
     * @dataProvider invalidTeamNameProvider
     */
    public function testIfTeamNameIsInvalid(string $name): void
    {
        $team = $this->createValidTeam();
        $team->setName($name);
        $violations = $this->validator->validate($team);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidTeamNameProvider(): array
    {
        return [
            ["W@tkins Glen"],
            ["/\/ercedes"],
            ["Mon€yTeam"],
            ["_"],
            ["..."],
            ["---"],
            ["'''"],
            ["nom   d'equipe"],
            ["A"],
            [""]
        ];
    }

    public function testIfTeamCountryIsValid(): void
    {
        $team = $this->createValidTeam();
        $violations = $this->validator->validate($team);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider invalidTeamCountryProvider
     */
    public function testIfTeamCountryIsInvalid(string $country): void
    {
        $team = $this->createValidTeam();
        $team->setCountry($country);
        $violations = $this->validator->validate($team);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidTeamCountryProvider(): array
    {
        return [
            ["La France, mais pas n'importe laquelle, celle du général De Gaulle"],
            ["huit"],
            ["KZK"],
            ["Almagne"]
        ];
    }

    public function createValidMember(): Member
    {
        $pgasly = new Member();
        $pgasly->setLastName("Gasly");
        $pgasly->setFirstName("Pierre");
        $pgasly->setCountry("FR");
        $pilot = new MemberRole();
        $pilot->setName("pilots");
        $pgasly->setMemberRole($pilot);
        $titular = new MemberStatus();
        $titular->setName("titulars");
        return $pgasly;
    }

    public function createValidFootballMember(): Member
    {
        $dlienard = new Member();
        $dlienard->setLastName("Liénard");
        $dlienard->setFirstName("Dimitri");
        $dlienard->setCountry("FR");
        $footballer = new MemberRole();
        $footballer->setName("footballeur");
        $dlienard->setMemberRole($footballer);
        $titular = new MemberStatus();
        $titular->setName("titulair");
        return $dlienard;
    }

    public function testIfMembersAreValid(): void
    {
        $team = $this->createValidTeam();
        $member = $this->createValidMember();
        $team->addMember($member);
        $team->getMembers();
        $violations = $this->validator->validate($team);
        $this->assertCount(0, $violations);
    }

    public function testIfNumberOfMembersIsValid(): void
    {
        $team = $this->createValidTeam();
        $sport = $this->createSportObject();
        $footMember = $this->createValidFootballMember();
        $team->addMember($footMember);
        $currentMembers = count($team->getMembers());
        $maxMembers = $sport->getMaxMembersByTeam();
        $violations = $this->validator->validate($team);
        $this->assertCount(0, $violations);
        $this->assertLessThanOrEqual($maxMembers, $currentMembers);
    }

    public function testIfNumberOfMembersIsInvalid(): void
    {
        $team = $this->createValidTeam();
        $sport = $this->createSportObject();
        $currentMember = $team->getMembers()->get(0);
        $team->removeMember($currentMember);
        $minMembers = $sport->getMinMembersByTeam();
        $violations = $this->validator->validate($team);
        $this->assertCount(1, $violations);
        $this->assertLessThan($minMembers, count($team->getMembers()));
    }
}
