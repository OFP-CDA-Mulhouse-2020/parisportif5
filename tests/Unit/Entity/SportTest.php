<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Sport;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Sport
 */
final class SportTest extends WebTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidSport(): Sport
    {
        $sport =  new Sport();
        $sport
            ->setName("Football")
            ->setMaxMembersByTeam(2)
            ->setMinMembersByTeam(1)
            ->setMaxTeamsByRun(2)
            ->setMinTeamsByRun(2)
            ->setCountry("BR")
            ->setRunType("fixture")
            ->setIndividualType(false)
            ->setCollectiveType(true);
        return $sport;
    }

    public function testIfNameIsNotNull(): void
    {
        $sport = $this->createValidSport();
        $violations = $this->validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    public function testIfNameIsNull(): void
    {
        $sport = $this->createValidSport();
        $sport->setName("");
        $violations = $this->validator->validate($sport);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testIfMaxNumberOfCompetitorsIsCorrect(): void
    {
        $sport = $this->createValidSport();
        $maxNumberOfCompetitors1 = 1;
        $maxNumberOfCompetitors2 = null;
        $sport->setMaxMembersByTeam($maxNumberOfCompetitors1);
        $violations = $this->validator->validate($sport);
        $this->assertCount(0, $violations);
        $sport->setMaxMembersByTeam($maxNumberOfCompetitors2);
        $violations = $this->validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    public function testIfMaxNumberOfCompetitorsIsIncorrect(): void
    {
        $sport = $this->createValidSport();
        $maxNumberOfCompetitors1 = 0;
        $maxNumberOfCompetitors2 = -1;
        $sport->setMaxMembersByTeam($maxNumberOfCompetitors1);
        $violations = $this->validator->validate($sport);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $sport->setMaxMembersByTeam($maxNumberOfCompetitors2);
        $violations = $this->validator->validate($sport);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testIfCountryIsValid(): void
    {
        $sport = $this->createValidSport();
        $violations = $this->validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    public function testRunTypeCompatible(): void
    {
        $runType1 = 'race';
        $runType2 = 'fixture';
        $sport = $this->createValidSport();
        $sport->setRunType($runType1);
        $violations = $this->validator->validate($sport);
        $this->assertCount(0, $violations);
        $sport->setRunType($runType2);
        $violations = $this->validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider runTypeUncompatibleProvider
     */
    public function testRunTypeUncompatible(string $runType): void
    {
        $sport = $this->createValidSport();
        $sport->setRunType('race');
        $sport->setRunType($runType);
        $this->assertSame('race', $sport->getRunType());
        $violations = $this->validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    public function runTypeUncompatibleProvider(): array
    {
        return [
            [' '],
            [''],
            ['chouette'],
            ["Fixtures"],
            ["Races"]
        ];
    }

    /**
     * @dataProvider validSportTypeSetProvider
     */
    public function testIfSportTypeIsValid(bool $individualType, bool $collectiveType): void
    {
        $sport = $this->createValidSport();
        $sport->setIndividualType($individualType);
        $sport->setCollectiveType($collectiveType);
        $violations = $this->validator->validate($sport);
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
        $sport = $this->createValidSport();
        $sport->setIndividualType(false);
        $sport->setCollectiveType(false);
        $violations = $this->validator->validate($sport);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testIfMaxTeamLimitIsValid(): void
    {
        $sport = $this->createValidSport();
        $maxNumberOfTeams1 = 2;
        $maxNumberOfTeams2 = null;
        $sport->setMaxTeamsByRun($maxNumberOfTeams1);
        $violations = $this->validator->validate($sport);
        $this->assertCount(0, $violations);
        $sport->setMaxTeamsByRun($maxNumberOfTeams2);
        $violations = $this->validator->validate($sport);
        $this->assertCount(0, $violations);
    }

    public function testIfMaxTeamLimitIsNotValid(): void
    {
        $sport = $this->createValidSport();
        $maxNumberOfTeams1 = 0;
        $maxNumberOfTeams2 = 1;
        $sport->setMaxTeamsByRun($maxNumberOfTeams1);
        $violations = $this->validator->validate($sport);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $sport->setMaxTeamsByRun($maxNumberOfTeams2);
        $violations = $this->validator->validate($sport);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testIfMinTeamLimitIsNotValid(): void
    {
        $sport = $this->createValidSport();
        $sport->setMinTeamsByRun(-1);
        $violations = $this->validator->validate($sport);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testIfMinNumberOfCompetitorsIsIncorrect(): void
    {
        $sport = $this->createValidSport();
        $sport->setMinMembersByTeam(-2);
        $violations = $this->validator->validate($sport);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }
}
