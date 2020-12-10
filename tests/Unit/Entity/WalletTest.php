<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Exception\WalletAmountException;
use App\Entity\Wallet;
use phpDocumentor\Reflection\Types\Null_;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

//use Symfony\Component\HttpKernel\KernelInterface;

class WalletTest extends WebTestCase
{

    private function walletInitialization(): Wallet
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

    private function kernelInitialization(): KernelInterface
    {
        $kernel = self::bootKernel();
        $kernel->boot();
        return $kernel;
    }

    public function testIfWalletIsNotNull(): void
    {
        $kernel = $this->kernelInitialization();
        $wallet = $this->walletInitialization();
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($wallet);
        $this->assertCount(0, $violations);
        $this->assertInstanceOf(Wallet::class, $wallet);
    }

    // public function testIfWalletIsNull(): void
    // {
    //     $wallet = null;
    //     $validator = Validation::createValidator();
    //     $violations = $validator->validate($wallet);
    //     $this->assertGreaterThanOrEqual(1, count($violations));
    // }

    public function testIfWalletIsNotNegative(): void
    {
        $kernel = $this->kernelInitialization();
        $wallet = $this->walletInitialization();
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($wallet);
        //throw new \Exception($violations);
        $this->assertCount(0, $violations);
    }

    public function testIfWalletIsNegative(): void
    {
        $kernel = $this->kernelInitialization();
        $wallet = $this->walletInitialization();
        $wallet->setAmount(-4);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($wallet);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testIfWalletAmountIsCorrect(): void
    {
        $wallet = $this->walletInitialization();
        $wallet->setAmount(0.4);
        $this->assertGreaterThanOrEqual(0, $wallet->getAmount());
    }
}
