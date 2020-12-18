<?php

namespace App\Tests\Unit\Entity;

use App\Entity\MemberRole;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MemberRoleTest extends KernelTestCase
{
    private function initializeMemberRole(): MemberRole
    {
        $memberRole =  new MemberRole();
        $memberRole->setName("pilot");
        return $memberRole;
    }

    private function initializeKernel(): KernelInterface
    {
        $kernel = self::bootKernel();
        $kernel->boot();
        return $kernel;
    }

    /**
     * @dataProvider validMemberRoleProvider
     */
    public function testIfMemberRoleIsValid(string $mR): void
    {
        $kernel = $this->initializeKernel();
        $memberRole = $this->initializeMemberRole();
        $memberRole->setName($mR);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($memberRole);
        $this->assertCount(0, $violations);
    }

    public function validMemberRoleProvider(): array
    {
        return [
            ["pilot"],
            ["footballer"],
            ["handballer"],
            ["tennsiplayer"],
            ["tabletennisplayer"]
        ];
    }

    /**
     * @dataProvider invalidMemberRoleProvider
     */
    public function testIfMemberRoleIsInvalid(string $mR): void
    {
        $kernel = $this->initializeKernel();
        $memberRole = $this->initializeMemberRole();
        $memberRole->setName($mR);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($memberRole);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidMemberRoleProvider(): array
    {
        return [
            ["driver"],
            ["player"],
            ["Jackson Richardson"],
            ["Guy Forg€t"]
        ];
    }
}
