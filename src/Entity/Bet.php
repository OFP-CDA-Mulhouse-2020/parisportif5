<?php

declare(strict_types=1);

namespace App\Entity;

use App\DataConverter\DateTimeStorageDataConverter;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BetRepository;

/**
 * @ORM\Entity(repositoryClass=BetRepository::class)
 * @UniqueEntity(
 *     fields={"user", "competition", "run", "betDate", "betCategory"},
 *     errorPath="betCategory",
 *     message="Ce paris est déjà enregistré."
 * )
 */
class Bet
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
     *     message="La côte du paris (multiplier par 10000) doit être un entier positif ou zéro."
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
     * @ORM\ManyToOne(targetEntity=BetCategory::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private BetCategory $betCategory;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $betDate;

    /** Sécurise le stockage des dates et heures */
    private DateTimeStorageDataConverter $dateTimeConverter;

    public function __construct()
    {
        $this->isWinning = null;
        $this->run = null;
        $this->team = null;
        $this->teamMember = null;
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

    public function getTarget(): ?object
    {
        return $this->teamMember ?? $this->team ?? $this->run ?? $this->competition;
    }

    public function getBetCategory(): ?BetCategory
    {
        return $this->betCategory;
    }

    public function setBetCategory(BetCategory $betCategory): self
    {
        $this->betCategory = $betCategory;

        return $this;
    }

    public function getBetDate(): ?\DateTimeImmutable
    {
        return $this->betDate;
    }

    public function setBetDate(\DateTimeInterface $betDate): self
    {
        $betDate = $this->dateTimeConverter->convertedToStoreDateTime($betDate);
        $this->betDate = $betDate;

        return $this;
    }

    public function setDateTimeConverter(DateTimeStorageDataConverter $dateTimeConverter): self
    {
        $this->dateTimeConverter = $dateTimeConverter;

        return $this;
    }
}
