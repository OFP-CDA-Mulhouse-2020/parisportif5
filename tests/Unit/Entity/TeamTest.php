<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Member;
use App\Entity\MemberRole;
use App\Entity\MemberStatus;
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
        return $team;
    }

    private function initializeKernel(): KernelInterface
    {
        $kernel = self::bootKernel();
        $kernel->boot();
        return $kernel;
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
            ["OLA"]
        ];
    }

    /**
     * @dataProvider invalidTeamNameProvider
     */
    public function testIfTeamNameIsInvalid(string $t): void
    {
        $kernel = $this->initializeKernel();
        $status = $this->initializeTeam();
        $status->setName($t);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($status);
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
        $pilot = new MemberRole();
        $pilot->setName("pilot");
        $pgasly->setMemberRole($pilot);
        $titular = new MemberStatus();
        $titular->setName("titular");
        return $pgasly;
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
        $member = $this->createValidMember();
        $team->addMember($member);
        $team->getMembers();
        $currentMembers = count($team->getMembers());
        $maxMembers = 2;
        $team->SetMaxMembers($maxMembers);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($team);
        $this->assertCount(0, $violations);
        $this->assertLessThanOrEqual($maxMembers, $currentMembers);
    }

    public function testIfNumberOfMembersIsInvalid()
    {
        $kernel = $this->initializeKernel();
        $team = $this->initializeTeam();
        $member = $this->createValidMember();
        $mambo = $this->createValidMember();
        $mimoune = $this->createValidMember();
        $team->addMember($member);
        $team->addMember($mambo);
        $team->addMember($mimoune);
        $team->getMembers();
        $currentMembers = count($team->getMembers());
        // var_dump($currentMembers);
        // die();
        $maxMembers = 2;
        $team->SetMaxMembers($maxMembers);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($team);
        $this->assertCount(0, $violations);
        $this->assertGreaterThan($maxMembers, $currentMembers);
    }
}
