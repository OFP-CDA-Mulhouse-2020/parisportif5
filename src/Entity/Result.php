<?php

namespace App\Entity;

use App\Repository\ResultRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ResultRepository::class)
 * @Assert\Expression(
 *     "this.getTeam() or this.getTeamMember()",
 *     message="L'équipe ou le membre doit être renseigné mais pas les deux"
 * )
 */
class Result
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(
     *     choices=Result::RESULT_TYPES,
     *     message="Choisisez un type de résultat valide"
     * )
     */
    private string $type;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(
     *     message="La valeur du résultat doit être un entier positif ou zéro"
     * )
     */
    private int $value;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $winner;

    /**
     * @ORM\ManyToOne(targetEntity=BetCategory::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private BetCategory $betCategory;

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

    public const RESULT_TYPES = ["time", "point"];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getWinner(): ?bool
    {
        return $this->winner;
    }

    public function setWinner(bool $winner): self
    {
        $this->winner = $winner;
        return $this;
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

    public function getTarget(): ?object
    {
        return $this->team ?? $this->teamMember;
    }
}
