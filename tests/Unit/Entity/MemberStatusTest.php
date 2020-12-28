<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\MemberStatus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \MemberStatus
 */
final class MemberStatusTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidMemberStatus(): MemberStatus
    {
        $status =  new MemberStatus();
        $status->setName("titular");
        return $status;
    }

    /**
     * @dataProvider validMemberStatusProvider
     */
    public function testIfMemberStatusIsCorrect(string $mS): void
    {
        $status = $this->createValidMemberStatus();
        $status->setName($mS);
        $violations = $this->validator->validate($status);
        $this->assertCount(0, $violations);
    }

    public function validMemberStatusProvider(): array
    {
        return [
            ["titular"],
            ["substitute"],
            ["injured"],
            ["suspended"],
            ["suspended_and_injured"],
            ["titular-or-suspended"]
        ];
    }

    /**
     * @dataProvider invalidMemberStatusProvider
     */
    public function testIfMemberStatusIsIncorrect(string $mS): void
    {
        $status = $this->createValidMemberStatus();
        $status->setName($mS);
        $violations = $this->validator->validate($status);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidMemberStatusProvider(): array
    {
        return [
            ["field player"],
            ["benched_"],
            ["-hurt"],
            ["onhôld"],
            [''],
            ['  ']
        ];
    }
}
