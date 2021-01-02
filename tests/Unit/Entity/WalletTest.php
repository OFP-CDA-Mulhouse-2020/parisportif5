<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Wallet;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Wallet
 */
final class WalletTest extends WebTestCase
{

    private function initializeWallet(): Wallet
    {
        $wallet =  new Wallet();
        $wallet->setAmount(4);
        return $wallet;
    }

    private ?ValidatorInterface $validator = null;

    public function setUp(): void
    {
        if (!$this->validator instanceof ValidatorInterface) {
            $kernel = self::bootKernel();
            $this->validator = $kernel->getContainer()->get('validator');
        }
    }

    private function initializeKernel(): KernelInterface
    {
        $kernel = self::bootKernel();
        $kernel->boot();
        return $kernel;
    }

    public function testIfWalletIsNotNull(): void
    {
        $kernel = $this->initializeKernel();
        $wallet = $this->initializeWallet();
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator'); //le kernel va chercher le composant validator
        $violations = $validator->validate($wallet);
        $this->assertCount(0, $violations);
        $this->assertInstanceOf(Wallet::class, $wallet);
    }

    public function testIfWalletIsNotNegative(): void
    {
        $kernel = $this->initializeKernel();
        $wallet = $this->initializeWallet();
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($wallet);
        $this->assertCount(0, $violations);
    }

    public function testIfWalletIsNegative(): void
    {
        $kernel = $this->initializeKernel();
        $wallet = $this->initializeWallet();
        $wallet->setAmount(-4);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($wallet);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testIfWalletAmountIsCorrect(): void
    {
        $wallet = $this->initializeWallet();
        $kernel = $this->initializeKernel();
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($wallet);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider incorrectWalletAmountProvider
     */
    public function testIfWalletAmountIsInCorrect(int $wA): void
    {
        $wallet = $this->initializeWallet();
        $kernel = $this->initializeKernel();
        $wallet->setAmount($wA);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($wallet);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function incorrectWalletAmountProvider(): array
    {
        return [
            [-14],
            [-1000000000]
            //["2"]
        ];
    }
}
