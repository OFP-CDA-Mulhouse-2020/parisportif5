<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Sport;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SportTest extends WebTestCase
{

    private function initializeSport(): Sport
    {
        $sport =  new Sport();
        $sport->setName("Football");
        $sport->setNumberOfCompetitors(1);
        $sport->setCountry("FR");
        $sport->setRunType("fixture");
        $sport->setIndividualType(false);
        $sport->setCollectiveType(true);
        return $sport;
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
        $sport = $this->initializeSport();
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    public function testIfNameIsNull(): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->initializeSport();
        $sport->setName("");
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertCount(1, $violations);
    }

    public function testIfNumberOfCompetitorsIsNotNull(): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->initializeSport();
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    public function testIfNumberOfCompetitorsIsIncorrect(): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->initializeSport();
        $sport->setNumberOfCompetitors(-2);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertCount(1, $violations);
    }

    public function testIfCountryIsValid(): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->initializeSport();
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    public function testIfRunTypeIsValid(): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->initializeSport();
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertCount(0, $violations);
    }

     /**
     * @dataProvider sportTypeProvider
     */
    public function testIfSportTypeIsValid(bool $cT, bool $iT): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->initializeSport();
        $sport->setIndividualType($iT);
        $sport->setCollectiveType($cT);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    public function sportTypeProvider(): array
    {
        return [
            [true, false],
            [false, true],
            [true, true]
        ];
    }

    public function testIfSportTypeIsNotValid(): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->initializeSport();
        $sport->setIndividualType(false);
        $sport->setCollectiveType(false);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertCount(1, $violations);
    }
}
