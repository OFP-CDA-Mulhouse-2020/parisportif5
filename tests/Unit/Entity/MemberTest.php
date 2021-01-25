<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Member;
use App\Entity\MemberRole;
use App\Entity\MemberStatus;
use App\Entity\ResultType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Member
 */
final class MemberTest extends WebTestCase
{
    private function initializeMember(): Member
    {
        $member =  new Member();
        $member->setLastName("Papin");
        $member->setFirstName("Jean-Pierre");
        $member->setCountry("FR");
        $member->setOdds(20000);
        return $member;
    }

    private function initializeMemberRole(string $memberRoleName): MemberRole
    {
        $memberRole =  new MemberRole();
        $memberRole->setName($memberRoleName);
        return $memberRole;
    }

    private function initializeMemberStatus(string $memberStatusName): MemberStatus
    {
        $memberStatus =  new MemberStatus();
        $memberStatus->setName($memberStatusName);
        return $memberStatus;
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
        $member = $this->initializeMember();
        $member->setOdds($odds);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($member);
        $this->assertCount(1, $violations);
    }

    public function testIfOddsIsValid(): void
    {
        $odds = 0;
        $kernel = $this->initializeKernel();
        $member = $this->initializeMember();
        $member->setOdds($odds);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($member);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider validLastNameProvider
     */
    public function testIfLastNameIsValid(string $lN): void
    {
        $kernel = $this->initializeKernel();
        $member = $this->initializeMember();
        $member->setLastName($lN);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($member);
        $this->assertCount(0, $violations);
    }

    public function validLastNameProvider(): array
    {
        return [
            ["Van Der Weg"],
            ["Höhenhausen"],
            ["Gonzalo-Viñales"],
            ["Åaland"],
            ["Sjålle"],
            ["Lindstrøm"],
            ["Üçkup"]
        ];
    }

    /**
     * @dataProvider invalidLastNameProvider
     */
    public function testIfLastNameIsInvalid(string $lN): void
    {
        $kernel = $this->initializeKernel();
        $member = $this->initializeMember();
        $member->setLastName($lN);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($member);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidLastNameProvider(): array
    {
        return [
            ["SPARRO\/\/"],
            ["H@land"],
            ["2"]
        ];
    }

    /**
     * @dataProvider validFirstNameProvider
     */
    public function testIfFirstNameIsValid(string $fN): void
    {
        $kernel = $this->initializeKernel();
        $member = $this->initializeMember();
        $member->setFirstName($fN);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($member);
        $this->assertCount(0, $violations);
    }

    public function validFirstNameProvider(): array
    {
        return [
            ["Antoinette"],
            ["Höx"],
            ["Nuño"],
            ["Åssel"],
            ["Sjåndra"],
            ["Pierre-Anthoine"],
            ["Joël"]
        ];
    }

    /**
     * @dataProvider invalidFirstNameProvider
     */
    public function testIfFirstNameIsInvalid(string $fN): void
    {
        $kernel = $this->initializeKernel();
        $member = $this->initializeMember();
        $member->setLastName($fN);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($member);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidFirstNameProvider(): array
    {
        return [
            ["\/\/ils0n"],
            ["H@rry"],
            ["Moumoute2"],
            ["♥"],
            [""]
        ];
    }

    public function testIfCountryIsValid(): void
    {
        $kernel = $this->initializeKernel();
        $member = $this->initializeMember();
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($member);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider invalidCountryProvider
     */
    public function testIfCountryIsInvalid(string $c): void
    {
        $kernel = $this->initializeKernel();
        $member = $this->initializeMember();
        $member->setCountry($c);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($member);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidCountryProvider(): array
    {
        return [
            ["La France, mais pas n'importe laquelle, celle du général De Gaulle"],
            ["huit"],
            ["KZK"],
            ["Almagne"]
        ];
    }

    public function testIfMemberRoleIsValid(): void
    {
        $kernel = $this->initializeKernel();
        $member = $this->initializeMember();

        $memberRole = $this->initializeMemberRole("footballer-avant");

        $member->setMemberRole($memberRole);
        $this->assertSame($memberRole, $member->getMemberRole());
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($member);
        $this->assertCount(0, $violations);
    }

    public function testIfMemberRoleIsInvalid(): void
    {
        $kernel = $this->initializeKernel();
        $member = $this->initializeMember();
        $memberRole = $this->initializeMemberRole("player_");
        $member->setMemberRole($memberRole);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($member);
        $this->assertCount(1, $violations);
    }

    public function testIfMemberStatusIsValid(): void
    {
        $kernel = $this->initializeKernel();
        $member = $this->initializeMember();

        $memberStatus = $this->initializeMemberStatus("titularization");

        $member->setMemberStatus($memberStatus);
        $this->assertSame($memberStatus, $member->getMemberStatus());
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($member);
        $this->assertCount(0, $violations);
    }

    public function testIfMemberStatusIsInvalid(): void
    {
        $kernel = $this->initializeKernel();
        $member = $this->initializeMember();
        $memberStatus = $this->initializeMemberStatus("on the bench");
        $member->setMemberStatus($memberStatus);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($member);
        $this->assertCount(1, $violations);
    }
}
