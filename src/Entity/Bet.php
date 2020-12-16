<?php

namespace App\Entity;

use App\Repository\BetRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BetRepository::class)
 */
class Bet implements FundStorageInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="La désignation du paris ne peut pas être vide",
     *     normalizer="trim"
     * )
     */
    private string $designation;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(
     *     message="La montant du paris en centimes doit être un entier positif ou zéro"
     * )
     */
    private int $amount;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(
     *     message="La côte du paris (multiplier par 10000) doit être un entier positif ou zéro"
     * )
     */
    private int $odds;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;
        return $this;
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

    public function getOdds(): ?int
    {
        return $this->odds;
    }

    public function setOdds(int $odds): self
    {
        $this->odds = $odds;
        return $this;
    }

    public function convertToCurrencyUnit(int $amount): float
    {
        return floatVal($amount * 0.01);
    }

    public function convertToOddsMultiplier(int $odds): float
    {
        return floatVal($odds * 0.0001);
    }

    public function convertCurrencyUnitToStoredData(float $amount): int
    {
        return intVal($amount * 100);
    }

    public function convertOddsMultiplierToStoredData(float $odds): int
    {
        return intVal($odds * 10000);
    }
}
