<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=WalletRepository::class)
 * @UniqueEntity(
 *     fields="user",
 *     message="Ce porte-monnaie est déjà enregistré."
 * )
 */
class Wallet implements FundStorageInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(
     *     message="Le montant du porte monnaie ne peut pas être négatif"
     * )
     */
    private int $amount;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="wallet", cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private User $user;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        // set the owning side of the relation if necessary
        /*if ($user->getWallet() !== $this) {
            $user->setWallet($this);
        }*/

        return $this;
    }

    public function convertToCurrencyUnit(int $amount): float
    {
        $this->amount = intval($amount / 100, 10);
        return $this->amount;
    }

    public function convertCurrencyUnitToStoredData(float $amount): int
    {
        $this->amount = intval($amount * 100, 10);
        return $this->amount;
    }
}
