<?php

namespace App\Entity;

use App\Entity\Exception\WalletAmountException;
use App\Repository\WalletRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(type="integer")
     * @Assert\NotNull(
     *     message="Le montant du wallet ne peut pas être null"
     * )
     * @Assert\GreaterThanOrEqual(0)(
     *     message="Le montant du wallet ne peut pas être négatif"
     * )
     */
    private int $amount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
