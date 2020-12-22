<?php

declare(strict_types=1);

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

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="wallet", cascade={"persist", "remove"})
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
        if ($user->getWallet() !== $this) {
            $user->setWallet($this);
        }

        return $this;
    }
}
