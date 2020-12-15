<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Member;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MemberTest extends WebTestCase
{

    private function initializeSport(): Member
    {
        $member =  new Member();
        $member->setLastName("Papin");
        $member->setFirstName("Jean-Pierre");
        return $member;
    }

    private function initializeKernel(): KernelInterface
    {
        $kernel = self::bootKernel();
        $kernel->boot();
        return $kernel;
    }

    /**
     * @dataProvider validLastNameProvider
     */
    public function testIfLastNameIsCorrect(string $lN): void
    {
        $kernel = $this->initializeKernel();
        $member = $this->initializeSport();
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
}
