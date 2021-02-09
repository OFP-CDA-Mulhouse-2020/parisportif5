<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Service\DateTimeStorageDataConverter;
use App\Entity\Bet;
use App\Entity\BetCategory;
use App\Entity\Competition;
use App\Entity\Member;
use App\Entity\Run;
use App\Entity\Sport;
use App\Entity\Team;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Bet
 */
final class BetTest extends WebTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidBet(): Bet
    {
        $converter = new DateTimeStorageDataConverter();
        $bet = new Bet($converter);
        $date = new \DateTimeImmutable("now", new \DateTimeZone("UTC"));
        $bet
            ->setDateTimeConverter($converter)
            ->setDesignation('paris')
            ->setAmount(100)
            ->setOdds('1.2')
            ->setBetDate($date);
        return $bet;
    }

    private function createUserObject(string $country = "FR"): User
    {
        $converter = new DateTimeStorageDataConverter();
        $user = new User($converter);
        $user
            ->setDateTimeConverter($converter)
            ->setCivility("Monsieur")
            ->setFirstName("Tintin")
            ->setLastName("Dupont")
            ->setBillingAddress("1 avenue st martin")
            ->setBillingCity("Colmar")
            ->setBillingPostcode("68000")
            ->setBillingCountry($country)
            ->setBirthDate(new \DateTimeImmutable("2000-10-10"))
            ->setPlainPassword("Azerty78")
            ->setPassword("hashpassword")
            ->setEmail("haddock@gmail.fr")
            ->setTimeZoneSelected("Europe/Paris")
            ->setResidenceProof("identity_card.pdf")
            ->setIdentityDocument("invoice.jpg");
        return $user;
    }

    private function createCompetitionObject(string $country = "FR"): Competition
    {
        $converter = new DateTimeStorageDataConverter();
        $competition = new Competition($converter);
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $competition
            ->setDateTimeConverter($converter)
            ->setName('Championnat inter-club')
            ->setStartDate($date->setTime(23, 59, 59, 1000000))
            ->setCountry($country)
            ->setMaxRuns(2)
            ->setMinRuns(0)
            ->setSport($this->createSportObject())
            ->addBetCategory($this->createBetCategoryObject());
        return $competition;
    }

    private function createSportObject(string $country = "BL"): Sport
    {
        $sport =  new Sport();
        $sport
            ->setName("Football")
            ->setMaxMembersByTeam(2)
            ->setMinMembersByTeam(1)
            ->setMaxTeamsByRun(2)
            ->setMinTeamsByRun(1)
            ->setCountry($country)
            ->setRunType("fixture")
            ->setIndividualType(false)
            ->setCollectiveType(true);
        return $sport;
    }

    private function createRunObject(Competition $competition, \DateTimeImmutable $date = null): Run
    {
        $converter = new DateTimeStorageDataConverter();
        $run = new Run($converter);
        $startDate = $date ?? new \DateTimeImmutable('+1 day', new \DateTimeZone('UTC'));
        $run
            ->setDateTimeConverter($converter)
            ->setName('run name')
            ->setEvent('event name')
            ->setStartDate($startDate)
            ->setCompetition($competition)
            ->addTeam($this->createTeamObject());
        return $run;
    }

    private function createTeamObject(string $country = "BL"): Team
    {
        $team =  new Team();
        $team
            ->setName("Trololo Futbol Klub")
            ->setCountry($country)
            ->setSport($this->createSportObject())
            ->addMember($this->createMemberObject())
            ->setOdds('2');
        return $team;
    }

    private function createMemberObject(string $lastName = "Poirot"): Member
    {
        $member = new Member();
        $member
            ->setLastName($lastName)
            ->setFirstName("Jean-Pierre")
            ->setCountry("FR")
            ->setOdds('2');
        return $member;
    }

    private function createBetCategoryObject(string $name = "resultw"): BetCategory
    {
        $betCategory = new BetCategory();
        $betCategory
            ->setName($name)
            ->setAllowDraw(false)
            ->setTarget("teams")
            ->setOnCompetition(false);
        return $betCategory;
    }

    /**
     * @dataProvider designationCompatibleProvider
     */
    public function testDesignationCompatible(string $designation): void
    {
        $bet = $this->createValidBet();
        $bet->setDesignation($designation);
        $violations = $this->validator->validate($bet);
        $this->assertCount(0, $violations);
    }

    public function designationCompatibleProvider(): array
    {
        return [
            ["paris sur le match PSG contre Truc, machin vainqueur"],
            ["PSG 1 <()[{]}>=+-*/\_?!;,:"]
        ];
    }

    public function testDesignationUncompatible(): void
    {
        $designation1 = '';
        $designation2 = '   ';
        $bet = $this->createValidBet();
        $bet->setDesignation($designation1);
        $violations = $this->validator->validate($bet);
        $this->assertCount(1, $violations);
        $bet->setDesignation($designation2);
        $violations = $this->validator->validate($bet);
        $this->assertCount(1, $violations);
    }

    public function testAmountCompatible(): void
    {
        $amount1 = 0;
        $amount2 = 1000000000;
        $bet = $this->createValidBet();
        $bet->setAmount($amount1);
        $violations = $this->validator->validate($bet);
        $this->assertCount(0, $violations);
        $bet->setAmount($amount2);
        $violations = $this->validator->validate($bet);
        $this->assertCount(0, $violations);
    }

    public function testAmountUncompatible(): void
    {
        $amount = -1;
        $bet = $this->createValidBet();
        $bet->setAmount($amount);
        $violations = $this->validator->validate($bet);
        $this->assertCount(1, $violations);
    }

    public function testOddsCompatible(): void
    {
        $odds1 = '0.00';
        $odds2 = '99999999.99';
        $bet = $this->createValidBet();
        $bet->setOdds($odds1);
        $violations = $this->validator->validate($bet);
        $this->assertCount(0, $violations);
        $bet->setOdds($odds2);
        $violations = $this->validator->validate($bet);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider oddsUncompatibleProvider
     */
    public function testOddsUncompatible(string $odds): void
    {
        $bet = $this->createValidBet();
        $bet->setOdds($odds);
        $violations = $this->validator->validate($bet);
        $this->assertCount(1, $violations);
    }

    public function oddsUncompatibleProvider(): array
    {
        return [
            ['-1'],
            ['100000000'],
            ['string']
        ];
    }

    public function testWonBet(): void
    {
        $bet = $this->createValidBet();
        $method = method_exists($bet, 'won');
        $this->assertTrue($method);
        $method = method_exists($bet, 'isWinning');
        $this->assertTrue($method);
        $this->assertNull($bet->isWinning());
        $bet->won();
        $this->assertTrue($bet->isWinning());
    }

    public function testLostBet(): void
    {
        $bet = $this->createValidBet();
        $method = method_exists($bet, 'lost');
        $this->assertTrue($method);
        $method = method_exists($bet, 'isWinning');
        $this->assertTrue($method);
        $this->assertNull($bet->isWinning());
        $bet->lost();
        $this->assertFalse($bet->isWinning());
    }

    public function testRestoreWithoutResultBet(): void
    {
        $bet = $this->createValidBet();
        $method = method_exists($bet, 'restoreWithoutResult');
        $this->assertTrue($method);
        $method = method_exists($bet, 'lost');
        $this->assertTrue($method);
        $method = method_exists($bet, 'isWinning');
        $this->assertTrue($method);
        $this->assertNull($bet->isWinning());
        $bet->lost();
        $this->assertFalse($bet->isWinning());
        $bet->restoreWithoutResult();
        $this->assertNull($bet->isWinning());
    }

    public function testUserUncompatible(): void
    {
        $bet = $this->createValidBet();
        $user = $this->createUserObject('XD');
        $bet->setUser($user);
        $violations = $this->validator->validate($bet, null, ['registration', 'login', 'profile', 'password_update', 'identifier_update', 'parameter']);
        $this->assertCount(1, $violations);
    }

    public function testUserCompatible(): void
    {
        $bet = $this->createValidBet();
        $user = $this->createUserObject();
        $bet->setUser($user);
        $this->assertSame($user, $bet->getUser());
        $violations = $this->validator->validate($bet, null, ['registration', 'login', 'profile', 'password_update', 'identifier_update', 'parameter']);
        $this->assertCount(0, $violations);
    }

    public function testCompetitionUncompatible(): void
    {
        $bet = $this->createValidBet();
        $competition = $this->createCompetitionObject('XD');
        $bet->setCompetition($competition);
        $violations = $this->validator->validate($bet);
        $this->assertCount(1, $violations);
    }

    public function testCompetitionCompatible(): void
    {
        $bet = $this->createValidBet();
        $competition = $this->createCompetitionObject();
        $bet->setCompetition($competition);
        $this->assertSame($competition, $bet->getCompetition());
        $violations = $this->validator->validate($bet);
        $this->assertCount(0, $violations);
    }

    public function testRunUncompatible(): void
    {
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $bet = $this->createValidBet();
        $competition = $this->createCompetitionObject();
        $run = $this->createRunObject($competition, $date);
        $bet->setRun($run);
        $violations = $this->validator->validate($bet);
        $this->assertCount(1, $violations);
    }

    public function testRunCompatible(): void
    {
        $bet = $this->createValidBet();
        $competition = $this->createCompetitionObject();
        $run = $this->createRunObject($competition);
        $bet->setRun($run);
        $this->assertSame($run, $bet->getRun());
        $violations = $this->validator->validate($bet);
        $this->assertCount(0, $violations);
    }

    public function testTeamUncompatible(): void
    {
        $bet = $this->createValidBet();
        $team = $this->createTeamObject('XD');
        $bet->setTeam($team);
        $violations = $this->validator->validate($bet);
        $this->assertCount(1, $violations);
    }

    public function testTeamCompatible(): void
    {
        $bet = $this->createValidBet();
        $team = $this->createTeamObject();
        $bet->setTeam($team);
        $this->assertSame($team, $bet->getTeam());
        $violations = $this->validator->validate($bet);
        $this->assertCount(0, $violations);
    }

    public function testTeamMemberUncompatible(): void
    {
        $bet = $this->createValidBet();
        $member = $this->createMemberObject('SPARRO\/\/');
        $bet->setTeamMember($member);
        $violations = $this->validator->validate($bet);
        $this->assertCount(1, $violations);
    }

    public function testTeamMemberCompatible(): void
    {
        $bet = $this->createValidBet();
        $member = $this->createMemberObject();
        $bet->setTeamMember($member);
        $this->assertSame($member, $bet->getTeamMember());
        $violations = $this->validator->validate($bet);
        $this->assertCount(0, $violations);
    }

    public function testBetCategoryUncompatible(): void
    {
        $bet = $this->createValidBet();
        $betCategory = $this->createBetCategoryObject('result_');
        $bet->setBetCategory($betCategory);
        $violations = $this->validator->validate($bet);
        $this->assertCount(1, $violations);
    }

    public function testBetCategoryCompatible(): void
    {
        $bet = $this->createValidBet();
        $betCategory = $this->createBetCategoryObject();
        $bet->setBetCategory($betCategory);
        $this->assertSame($betCategory, $bet->getBetCategory());
        $violations = $this->validator->validate($bet);
        $this->assertCount(0, $violations);
    }
}
