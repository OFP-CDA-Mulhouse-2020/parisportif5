<?php

namespace App\Entity;

use App\Entity\Exception\WalletAmountException;
use App\Repository\WalletRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WalletRepository::class)
 */
class Wallet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="float")
     */
    private float $amount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        if ($amount < 0) {
            throw new WalletAmountException("Le montant du wallet doit être poisitf ou nul");
        }
        $this->amount = $amount;

        return $this;
    }
}
