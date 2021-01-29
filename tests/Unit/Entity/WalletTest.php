<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Wallet;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Wallet
 */
final class WalletTest extends WebTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function initializeWallet(): Wallet
    {
        $wallet =  new Wallet();
        $wallet->setAmount(4);
        return $wallet;
    }

    public function testIfWalletIsNotNull(): void
    {
        $wallet = $this->initializeWallet();
        $violations = $this->validator->validate($wallet);
        $this->assertCount(0, $violations);
        $this->assertInstanceOf(Wallet::class, $wallet);
    }

    public function testIfWalletIsNotNegative(): void
    {
        $wallet = $this->initializeWallet();
        $violations = $this->validator->validate($wallet);
        $this->assertCount(0, $violations);
    }

    public function testIfWalletIsNegative(): void
    {
        $wallet = $this->initializeWallet();
        $wallet->setAmount(-4);
        $violations = $this->validator->validate($wallet);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testIfWalletAmountIsCorrect(): void
    {
        $wallet = $this->initializeWallet();
        $violations = $this->validator->validate($wallet);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider incorrectWalletAmountProvider
     */
    public function testIfWalletAmountIsInCorrect(int $amount): void
    {
        $wallet = $this->initializeWallet();
        $wallet->setAmount($amount);
        $violations = $this->validator->validate($wallet);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function incorrectWalletAmountProvider(): array
    {
        return [
            [-14],
            [-1000000000]
        ];
    }
}
