<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\MemberStatus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \MemberStatus
 */
final class MemberStatusTest extends KernelTestCase
{
    private function initializeMemberStatus(): MemberStatus
    {
        $status =  new MemberStatus();
        $status->setName("titular");
        return $status;
    }

    private function initializeKernel(): KernelInterface
    {
        $kernel = self::bootKernel();
        $kernel->boot();
        return $kernel;
    }

    /**
     * @dataProvider validMemberStatusProvider
     */
    public function testIfMemberStatusIsCorrect(string $mS): void
    {
        $kernel = $this->initializeKernel();
        $status = $this->initializeMemberStatus();
        $status->setName($mS);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($status);
        $this->assertCount(0, $violations);
    }

    public function validMemberStatusProvider(): array
    {
        return [
            ["titular"],
            ["substitute"],
            ["injured"],
            ["suspended"]
        ];
    }

    /**
     * @dataProvider invalidMemberStatusProvider
     */
    public function testIfMemberStatusIsIncorrect(string $mS): void
    {
        $kernel = $this->initializeKernel();
        $status = $this->initializeMemberStatus();
        $status->setName($mS);
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($status);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidMemberStatusProvider(): array
    {
        return [
            ["field player"],
            ["benched"],
            ["hurt"],
            ["on hold"]
        ];
    }
}
