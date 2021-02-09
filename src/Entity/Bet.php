<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BetRepository;

/**
 * @ORM\Entity(repositoryClass=BetRepository::class)
 * @UniqueEntity(
 *     fields={"user", "betDate"},
 *     errorPath="betDate",
 *     message="Le nombre de paris par minute est limité à 6 pour des raisons de sécurité."
 * )
 */
class Bet extends AbstractEntity
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
     *     message="Le montant du paris en centimes doit être un entier positif ou zéro"
     * )
     */
    private int $amount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\Type(
     *     type="numeric",
     *     message="La côte du paris doit être un nombre entier ou réel."
     * )
     * @Assert\PositiveOrZero(
     *     message="La côte du paris doit être un positif ou zéro."
     * )
     * @Assert\LessThan(
     *     value=100000000,
     *     message="La côte du paris doit être inférieur à {{ compared_value }}."
     * )
     */
    private string $odds;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isWinning = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="onGoingBets")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity=BetCategory::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private BetCategory $betCategory;

    /**
     * @ORM\ManyToOne(targetEntity=Competition::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private Competition $competition;

    /**
     * @ORM\ManyToOne(targetEntity=Run::class)
     * @Assert\Valid
     */
    private ?Run $run = null;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class)
     * @Assert\Valid
     */
    private ?Team $team = null;

    /**
     * @ORM\ManyToOne(targetEntity=Member::class)
     * @Assert\Valid
     */
    private ?Member $teamMember = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $betDate;

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

    public function getOdds(): ?string
    {
        return $this->odds;
    }

    public function setOdds(string $odds): self
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

    public function isWinning(): ?bool
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

    public function getBetCategory(): ?BetCategory
    {
        return $this->betCategory;
    }

    public function setBetCategory(BetCategory $betCategory): self
    {
        $this->betCategory = $betCategory;
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

    public function getBetDate(): ?\DateTimeImmutable
    {
        return $this->betDate;
    }

    public function setBetDate(\DateTimeInterface $betDate): self
    {
        $betDate = $this->convertedToStoreDateTime($betDate);
        $this->betDate = $betDate;
        return $this;
    }

    public function getTarget(): object
    {
        return $this->run ?? $this->competition;
    }

    public function getSelect(): ?object
    {
        return $this->teamMember ?? $this->team;
    }
}
