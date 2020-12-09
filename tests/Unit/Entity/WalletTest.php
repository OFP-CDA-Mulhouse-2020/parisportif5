<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Exception\WalletAmountException;
use App\Entity\Wallet;
use PHPUnit\Framework\TestCase;

class WalletTest extends TestCase
{

    private function walletInitialization(): Wallet
    {
        return new Wallet();
    }

    public function testIfWalletIsNotNull(): void
    {
        $wallet = $this->walletInitialization();
        $this->assertNotNull($wallet, "Le wallet ne doit pas être nul");
        $this->assertInstanceOf(Wallet::class, $wallet);
    }

    public function testIfWalletIsNotNegative(): void
    {
        $wallet = $this->walletInitialization();
        $this->expectException(WalletAmountException::class);
        $wallet->setAmount(-4);
    }

    public function testIfWalletAmountIsCorrect(): void
    {
        $wallet = $this->walletInitialization();
        $wallet->setAmount(0.4);
        $this->assertGreaterThanOrEqual(0, $wallet->getAmount());
    }
}
