<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Wallet;
use Symfony\Component\Validator\Constraints as Assert;

final class WalletRetireFundsFormModel
{
    private Wallet $wallet;
    private string $currency = "EUR";
    /**
     * @Assert\PositiveOrZero(
     *     message="Le montant du porte monnaie ne peut pas être négatif."
     * )
     */
    private int $walletAmount = 0;
    /**
     * @Assert\Positive(
     *     message="Le montant a retirer du porte monnaie ne peut pas être négatif."
     * )
     * @Assert\LessThanOrEqual(
     *     propertyPath="walletAmount",
     *     message="Le montant doit être inférieur ou égale à la somme {{ compared_value }} du porte-monnaie."
     * )
     */
    private int $amountRetire = 0;

    public function __construct(
        Wallet $wallet,
        int $walletAmount
    ) {
        $this->wallet = $wallet;
        $this->walletAmount = $walletAmount;
        $this->amountRetire = $this->walletAmount;
    }

    public function getWallet(): Wallet
    {
        return $this->wallet;
    }

    /**
     * @Assert\IsTrue(
     *     message="Le montant saisi n'est pas autorisé a être retiré du porte-monnaie."
     * )
     */
    public function isValidWalletSubtraction(): bool
    {
        return !empty($this->amountRetire) ? $this->wallet->isValidSubtraction($this->amountRetire) : false;
    }

    public function getWalletAmount(): int
    {
        return $this->walletAmount;
    }

    public function getAmountRetire(): int
    {
        return $this->amountRetire;
    }

    public function setAmountRetire(int $amountRetire): void
    {
        $this->amountRetire = $amountRetire;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
