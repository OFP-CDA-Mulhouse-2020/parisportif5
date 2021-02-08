<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Member;
use App\Entity\MemberRole;
use App\Entity\MemberStatus;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Member
 */
final class MemberTest extends WebTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidMember(): Member
    {
        $member =  new Member();
        $member->setLastName("Papin");
        $member->setFirstName("Jean-Pierre");
        $member->setCountry("FR");
        $member->setOdds('2');
        return $member;
    }

    private function createMemberRoleObject(string $memberRoleName): MemberRole
    {
        $memberRole =  new MemberRole();
        $memberRole->setName($memberRoleName);
        return $memberRole;
    }

    private function createMemberStatusObject(string $memberStatusName): MemberStatus
    {
        $memberStatus =  new MemberStatus();
        $memberStatus->setName($memberStatusName);
        return $memberStatus;
    }

    public function testOddsCompatible(): void
    {
        $odds1 = '0';
        $odds2 = '10000000';
        $member = $this->createValidMember();
        $member->setOdds($odds1);
        $violations = $this->validator->validate($member);
        $this->assertCount(0, $violations);
        $member->setOdds($odds2);
        $violations = $this->validator->validate($member);
        $this->assertCount(0, $violations);
    }

    public function testOddsUncompatible(): void
    {
        $odds1 = '-1';
        $odds2 = '100000000';
        $member = $this->createValidMember();
        $member->setOdds($odds1);
        $violations = $this->validator->validate($member);
        $this->assertCount(1, $violations);
        $member->setOdds($odds2);
        $violations = $this->validator->validate($member);
        $this->assertCount(1, $violations);
    }

    /**
     * @dataProvider validLastNameProvider
     */
    public function testIfLastNameIsValid(string $lastName): void
    {
        $member = $this->createValidMember();
        $member->setLastName($lastName);
        $violations = $this->validator->validate($member);
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
    public function testIfLastNameIsInvalid(string $lastName): void
    {
        $member = $this->createValidMember();
        $member->setLastName($lastName);
        $violations = $this->validator->validate($member);
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
    public function testIfFirstNameIsValid(string $firstName): void
    {
        $member = $this->createValidMember();
        $member->setFirstName($firstName);
        $violations = $this->validator->validate($member);
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
    public function testIfFirstNameIsInvalid(string $firstName): void
    {
        $member = $this->createValidMember();
        $member->setFirstName($firstName);
        $violations = $this->validator->validate($member);
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
        $member = $this->createValidMember();
        $violations = $this->validator->validate($member);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider invalidCountryProvider
     */
    public function testIfCountryIsInvalid(string $country): void
    {
        $member = $this->createValidMember();
        $member->setCountry($country);
        $violations = $this->validator->validate($member);
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
        $member = $this->createValidMember();
        $memberRole = $this->createMemberRoleObject("footballer-avant");
        $member->setMemberRole($memberRole);
        $this->assertSame($memberRole, $member->getMemberRole());
        $violations = $this->validator->validate($member);
        $this->assertCount(0, $violations);
    }

    public function testIfMemberRoleIsInvalid(): void
    {
        $member = $this->createValidMember();
        $memberRole = $this->createMemberRoleObject("player_");
        $member->setMemberRole($memberRole);
        $violations = $this->validator->validate($member);
        $this->assertCount(1, $violations);
    }

    public function testIfMemberStatusIsValid(): void
    {
        $member = $this->createValidMember();
        $memberStatus = $this->createMemberStatusObject("titularization");
        $member->setMemberStatus($memberStatus);
        $this->assertSame($memberStatus, $member->getMemberStatus());
        $violations = $this->validator->validate($member);
        $this->assertCount(0, $violations);
    }

    public function testIfMemberStatusIsInvalid(): void
    {
        $member = $this->createValidMember();
        $memberStatus = $this->createMemberStatusObject("on the bench");
        $member->setMemberStatus($memberStatus);
        $violations = $this->validator->validate($member);
        $this->assertCount(1, $violations);
    }
}
