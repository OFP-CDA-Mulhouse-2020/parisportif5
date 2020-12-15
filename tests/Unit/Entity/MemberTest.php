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
        return $member;
    }

    private function initializeKernel(): KernelInterface
    {
        $kernel = self::bootKernel();
        $kernel->boot();
        return $kernel;
    }

    public function testIfNameIsNotNull(): void
    {
        $kernel = $this->initializeKernel();
        $member = $this->initializeSport();
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($member);
        $this->assertCount(0, $violations);
    }
}
