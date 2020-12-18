<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Bet;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BetTest extends KernelTestCase
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
}
