<?php

declare(strict_types=1);

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

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isWinning;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="onGoingBets")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private User $user;

    /**
     * @ORM\OneToOne(targetEntity=Competition::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private Competition $competition;

    /**
     * @ORM\OneToOne(targetEntity=Run::class, cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private ?Run $run;

    /**
     * @ORM\OneToOne(targetEntity=Team::class, cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private ?Team $team;

    /**
     * @ORM\OneToOne(targetEntity=Member::class, cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private ?Member $teamMember;

    /**
     * @ORM\OneToOne(targetEntity=BetType::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private BetType $betType;

    public function __construct()
    {
        $this->isWinning = null;
    }

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCompetition(): ?Competition
    {
        return $this->competition;
    }

    public function setCompetition(Competition $competition): self
    {
        $this->competition = $competition;
        return $this;
    }

    public function getRun(): ?Run
    {
        return $this->run;
    }

    public function setRun(?Run $run): self
    {
        $this->run = $run;
        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;
        return $this;
    }

    public function getTeamMember(): ?Member
    {
        return $this->teamMember;
    }

    public function setTeamMember(?Member $teamMember): self
    {
        $this->teamMember = $teamMember;
        return $this;
    }

    public function getBetType(): ?BetType
    {
        return $this->betType;
    }

    public function setBetType(BetType $betType): self
    {
        $this->betType = $betType;
        return $this;
    }

    public function hasWon(): ?bool
    {
        return $this->isWinning;
    }

    public function won(): void
    {
        $this->isWinning = true;
    }

    public function lost(): void
    {
        $this->isWinning = false;
    }

    public function restoreWithoutResult(): void
    {
        $this->isWinning = null;
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

    public function getTarget(): ?object
    {
        return $this->teamMember ?? $this->team ?? $this->run ?? $this->competition;
    }
}
