<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\BetCategory;
use App\Entity\Sport;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Sport
 */
final class SportTest extends WebTestCase
{

    private function createValidSport(): Sport
    {
        $sport =  new Sport();
        $sport
            ->setName("Football")
            ->setMaxMembersByTeam(2)
            ->setMinMembersByTeam(1)
            ->setMaxTeamsByRun(2)
            ->setCountry("FR")
            ->setRunType("fixture")
            ->setIndividualType(false)
            ->setCollectiveType(true);
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
        $sport = $this->createValidSport();
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    public function testIfNameIsNull(): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->createValidSport();
        $sport->setName("");
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testIfMaxNumberOfCompetitorsIsCorrect(): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->createValidSport();
        $maxNumberOfCompetitors1 = 1;
        $maxNumberOfCompetitors2 = null;
        $sport->setMaxMembersByTeam($maxNumberOfCompetitors1);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertCount(0, $violations);
        $sport->setMaxMembersByTeam($maxNumberOfCompetitors2);
        $violations = $validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    public function testIfMaxNumberOfCompetitorsIsIncorrect(): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->createValidSport();
        $maxNumberOfCompetitors1 = 0;
        $maxNumberOfCompetitors2 = -1;
        $sport->setMaxMembersByTeam($maxNumberOfCompetitors1);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $sport->setMaxMembersByTeam($maxNumberOfCompetitors2);
        $violations = $validator->validate($sport);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testIfCountryIsValid(): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->createValidSport();
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    public function testIfRunTypeIsValid(): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->createValidSport();
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider validSportTypeSetProvider
     */
    public function testIfSportTypeIsValid(bool $individualType, bool $collectiveType): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->createValidSport();
        $sport->setIndividualType($individualType);
        $sport->setCollectiveType($collectiveType);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    public function validSportTypeSetProvider(): array
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
        $sport = $this->createValidSport();
        $sport->setIndividualType(false);
        $sport->setCollectiveType(false);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testIfTeamLimitIsValid(): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->createValidSport();
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    public function testIfTeamLimitIsNotValid(): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->createValidSport();
        $sport->setMaxTeamsByRun(-1);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testIfMinNumberOfCompetitorsIsIncorrect(): void
    {
        $kernel = $this->initializeKernel();
        $sport = $this->createValidSport();
        $sport->setMinMembersByTeam(-2);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($sport);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }
}
