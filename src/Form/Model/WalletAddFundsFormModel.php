<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Wallet;
use Symfony\Component\Validator\Constraints as Assert;

final class WalletAddFundsFormModel
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
     *     message="Le montant a ajouter au porte monnaie ne peut pas être négatif."
     * )
     */
    private ?int $amountAdd = null;

    public function __construct(
        Wallet $wallet,
        int $walletAmount
    ) {
        $this->wallet = $wallet;
        $this->walletAmount = $walletAmount;
    }

    public function getWallet(): Wallet
    {
        return $this->wallet;
    }

    /**
     * @Assert\IsTrue(
     *     message="Le montant saisi n'est pas autorisé a être ajouté au porte-monnaie."
     * )
     */
    public function isValidWalletAddition(): bool
    {
        return !empty($this->amountAdd) ? $this->wallet->isValidAddition($this->amountAdd) : false;
    }

    public function getWalletAmount(): int
    {
        return $this->walletAmount;
    }

    public function getAmountAdd(): int
    {
        return $this->amountAdd;
    }

    public function setAmountAdd(int $amountAdd): void
    {
        $this->amountAdd = $amountAdd;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
