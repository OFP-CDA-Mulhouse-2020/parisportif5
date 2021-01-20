<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\MemberRole;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \MemberRole
 */
final class MemberRoleTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidMemberRole(): MemberRole
    {
        $memberRole =  new MemberRole();
        $memberRole->setName("pilot");
        return $memberRole;
    }

    /**
     * @dataProvider validMemberRoleProvider
     */
    public function testIfMemberRoleIsValid(string $mR): void
    {
        $memberRole = $this->createValidMemberRole();
        $memberRole->setName($mR);
        $violations = $this->validator->validate($memberRole);
        $this->assertCount(0, $violations);
    }

    public function validMemberRoleProvider(): array
    {
        return [
            ["PILOTz"],
            ["footballerz"],
            ["handballerz"],
            ["tennis-playerz"],
            ["table-tennis_playerz"]
        ];
    }

    /**
     * @dataProvider invalidMemberRoleProvider
     */
    public function testIfMemberRoleIsIncorrect(string $mR): void
    {
        $memberRole = $this->createValidMemberRole();
        $memberRole->setName($mR);
        $violations = $this->validator->validate($memberRole);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidMemberRoleProvider(): array
    {
        return [
            ["driver_"],
            ["-player"],
            ["Jâckson Richardson"],
            ["Guy_Forg€t"],
            [''],
            ['  ']
        ];
    }
}
