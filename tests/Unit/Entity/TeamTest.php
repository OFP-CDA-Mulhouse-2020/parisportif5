<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Member;
use App\Entity\MemberRole;
use App\Entity\MemberStatus;
use App\Entity\Sport;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

//tester si l'equipe est liee au sport
//tester la limite de membres par equipe en fonction du sport

/**
 * @covers \Team
 */
final class TeamTest extends KernelTestCase
{
    private function initializeTeam(): Team
    {
        $team =  new Team();
        $team->setName("RC Strasbourg Alsace");
        $team->setCountry("FR");
        $team->setSport($this->createSportObject());
        $team->addMember($this->createMemberObject());
        $team->setOdds(20000);
        return $team;
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

    private function initializeKernel(): KernelInterface
    {
        $kernel = self::bootKernel();
        $kernel->boot();
        return $kernel;
    }


    public function testIfOddsIsInvalid(): void
    {
        $odds = -1;
        $kernel = $this->initializeKernel();
        $team = $this->initializeTeam();
        $team->setOdds($odds);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($team);
        $this->assertCount(1, $violations);
    }

    public function testIfOddsIsValid(): void
    {
        $odds = 0;
        $kernel = $this->initializeKernel();
        $team = $this->initializeTeam();
        $team->setOdds($odds);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($team);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider validTeamNameProvider
     */
    public function testIfTeamNameIsValid(string $t): void
    {
        $kernel = $this->initializeKernel();
        $team = $this->initializeTeam();
        $team->setName($t);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($team);
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
    public function testIfTeamNameIsInvalid(string $t): void
    {
        $kernel = $this->initializeKernel();
        $team = $this->initializeTeam();
        $team->setName($t);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($team);
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
        $kernel = $this->initializeKernel();
        $team = $this->initializeTeam();
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($team);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider invalidTeamCountryProvider
     */
    public function testIfTeamCountryIsInvalid(string $c): void
    {
        $kernel = $this->initializeKernel();
        $team = $this->initializeTeam();
        $team->setCountry($c);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($team);
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
        $kernel = $this->initializeKernel();
        $team = $this->initializeTeam();
        $member = $this->createValidMember();
        $team->addMember($member);
        $team->getMembers();
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($team);
        $this->assertCount(0, $violations);
    }

    public function testIfNumberOfMembersIsValid()
    {
        $kernel = $this->initializeKernel();
        $team = $this->initializeTeam();
        $sport = $this->createSportObject();
        $footMember = $this->createValidFootballMember();
        $team->addMember($footMember);
        $currentMembers = count($team->getMembers());
        $maxMembers = $sport->getMaxMembersByTeam();
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($team);
        $this->assertCount(0, $violations);
        $this->assertLessThanOrEqual($maxMembers, $currentMembers);
    }

    public function testIfNumberOfMembersIsInvalid()
    {
        $kernel = $this->initializeKernel();
        $team = $this->initializeTeam();
        $sport = $this->createSportObject();
        $currentMember = $team->getMembers()->get(0);
        $team->removeMember($currentMember);
        $minMembers = $sport->getMinMembersByTeam();
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($team);
        $this->assertCount(1, $violations);
        $this->assertLessThan($minMembers, count($team->getMembers()));
    }
}
