<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Bet;
use App\Entity\Competition;
use App\Entity\Member;
use App\Entity\Run;
use App\Entity\Team;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Bet
 */
final class BetTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    //throw new \Exception($violations);
    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidBet(): Bet
    {
        $bet = new Bet();
        $bet
            ->setDesignation('paris')
            ->setAmount(100)
            ->setOdds(12000);
        return $bet;
    }

    private function createUserObject(string $country = "FR"): User
    {
        $user = new User();
        $user
            ->setCivility("Monsieur")
            ->setFirstName("Tintin")
            ->setLastName("Dupont")
            ->setBillingAddress("1 avenue st martin")
            ->setBillingCity("Colmar")
            ->setBillingPostcode("68000")
            ->setBillingCountry($country)
            ->setBirthDate(new \DateTimeImmutable("2000-10-10"))
            ->setPassword("Azerty78")
            ->setEmail("dupond.t@orange.fr")
            ->setTimeZoneSelected("Europe/Paris");
        return $user;
    }

    private function createCompetitionObject(string $country = "FR"): Competition
    {
        $competition = new Competition();
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $competition
            ->setName('name')
            ->setStartDate($date->setTime(23, 59, 59, 1000000))
            ->setCountry($country)
            ->setMaxRuns(1);
        return $competition;
    }

    private function createRunObject(\DateTimeImmutable $date = null): Run
    {
        $run = new Run();
        $startDate = $date ?? new \DateTimeImmutable('+1 day', new \DateTimeZone('UTC'));
        $run
            ->setName('run name')
            ->setEvent('event name')
            ->setStartDate($startDate);
        return $run;
    }

    private function createTeamObject(string $country = "FR"): Team
    {
        $team =  new Team();
        $team
            ->setName("RC Strasbourg Alsace")
            ->setCountry($country);
        return $team;
    }

    private function createMemberObject(string $lastName = "Jean"): Member
    {
        $member = new Member();
        $member
            ->setLastName($lastName)
            ->setFirstName("Jean-Pierre");
        return $member;
    }

    /**
     * @dataProvider designationCompatibleProvider
     */
    public function testDesignationCompatible(string $designation)
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

    public function testDesignationUncompatible()
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

    public function testAmountCompatible()
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

    public function testAmountUncompatible()
    {
        $amount = -1;
        $bet = $this->createValidBet();
        $bet->setAmount($amount);
        $violations = $this->validator->validate($bet);
        $this->assertCount(1, $violations);
    }

    public function testOddsCompatible()
    {
        $odds1 = 0;
        $odds2 = 1000000000;
        $bet = $this->createValidBet();
        $bet->setOdds($odds1);
        $violations = $this->validator->validate($bet);
        $this->assertCount(0, $violations);
        $bet->setOdds($odds2);
        $violations = $this->validator->validate($bet);
        $this->assertCount(0, $violations);
    }

    public function testOddsUncompatible()
    {
        $odds = -1;
        $bet = $this->createValidBet();
        $bet->setOdds($odds);
        $violations = $this->validator->validate($bet);
        $this->assertCount(1, $violations);
    }

    public function testMethodConvertToCurrencyUnitReturnValue(): void
    {
        $bet = $this->createValidBet();
        $result = method_exists($bet, 'convertToCurrencyUnit');
        $this->assertTrue($result);
        $result = $bet->convertToCurrencyUnit(500);
        $this->assertIsFloat($result);
        //$this->assertSame(5.0, $result);
    }

    public function testMethodConvertToOddsMultiplierReturnValue(): void
    {
        $bet = $this->createValidBet();
        $result = method_exists($bet, 'convertToOddsMultiplier');
        $this->assertTrue($result);
        $result = $bet->convertToOddsMultiplier(15000);
        $this->assertIsFloat($result);
        //$this->assertSame(1.5, $result);
    }

    public function testMethodConvertCurrencyUnitToStoredDataReturnValue(): void
    {
        $bet = $this->createValidBet();
        $result = method_exists($bet, 'convertCurrencyUnitToStoredData');
        $this->assertTrue($result);
        $result = $bet->convertCurrencyUnitToStoredData(5.0);
        $this->assertIsInt($result);
        //$this->assertSame(500, $result);
    }

    public function testMethodConvertOddsMultiplierToStoredDataReturnValue(): void
    {
        $bet = $this->createValidBet();
        $result = method_exists($bet, 'convertOddsMultiplierToStoredData');
        $this->assertTrue($result);
        $result = $bet->convertOddsMultiplierToStoredData(1.5);
        $this->assertIsInt($result);
        //$this->assertSame(15000, $result);
    }

    public function testWonBet(): void
    {
        $bet = $this->createValidBet();
        $method = method_exists($bet, 'won');
        $this->assertTrue($method);
        $method = method_exists($bet, 'hasWon');
        $this->assertTrue($method);
        $this->assertNull($bet->hasWon());
        $bet->won();
        $this->assertTrue($bet->hasWon());
    }

    public function testLostBet(): void
    {
        $bet = $this->createValidBet();
        $method = method_exists($bet, 'lost');
        $this->assertTrue($method);
        $method = method_exists($bet, 'hasWon');
        $this->assertTrue($method);
        $this->assertNull($bet->hasWon());
        $bet->lost();
        $this->assertFalse($bet->hasWon());
    }

    public function testRestoreWithoutResultBet(): void
    {
        $bet = $this->createValidBet();
        $method = method_exists($bet, 'restoreWithoutResult');
        $this->assertTrue($method);
        $method = method_exists($bet, 'lost');
        $this->assertTrue($method);
        $method = method_exists($bet, 'hasWon');
        $this->assertTrue($method);
        $this->assertNull($bet->hasWon());
        $bet->lost();
        $this->assertFalse($bet->hasWon());
        $bet->restoreWithoutResult();
        $this->assertNull($bet->hasWon());
    }

    public function testUserUncompatible(): void
    {
        $bet = $this->createValidBet();
        $user = $this->createUserObject('XD');
        $bet->setUser($user);
        $violations = $this->validator->validate($bet);
        $this->assertCount(1, $violations);
    }

    public function testUserCompatible(): void
    {
        $bet = $this->createValidBet();
        $user = $this->createUserObject();
        $bet->setUser($user);
        $this->assertSame($user, $bet->getUser());
        $violations = $this->validator->validate($bet);
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
        $run = $this->createRunObject($date);
        $bet->setRun($run);
        $violations = $this->validator->validate($bet);
        $this->assertCount(1, $violations);
    }

    public function testRunCompatible(): void
    {
        $bet = $this->createValidBet();
        $run = $this->createRunObject();
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

    public function testMethodGetTargetExpectedReturnValue(): void
    {
        $bet = $this->createValidBet();
        $method = method_exists($bet, 'getTarget');
        $this->assertTrue($method);
        $competition = $this->createCompetitionObject();
        $bet->setCompetition($competition);
        $result = $bet->getTarget();
        $this->assertIsObject($result);
        $this->assertInstanceOf(Competition::class, $result);
        $member = $this->createMemberObject();
        $bet->setTeamMember($member);
        $result = $bet->getTarget();
        $this->assertIsObject($result);
        $this->assertInstanceOf(Member::class, $result);
    }
}
