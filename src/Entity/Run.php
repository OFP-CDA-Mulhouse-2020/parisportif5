<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RunRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RunRepository::class)
 */
class Run
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
     *     message="Le nom de la course ou du match ne peut pas être vide",
     *     normalizer="trim"
     * )
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le nom de l'évènement ne peut pas être vide",
     *     normalizer="trim"
     * )
     */
    private string $event;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\GreaterThanOrEqual(
     *     value="tomorrow UTC",
     *     message="La date du début de la course ou du match doit être supérieur ou égale au {{ compared_value }} UTC"
     * )
     */
    private \DateTimeImmutable $startDate;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\GreaterThan(
     *     propertyPath="startDate",
     *     message="La date de fin de la course ou du match doit être supérieur à la date du début de celle-ci"
     * )
     */
    private \DateTimeImmutable $endDate;

    /**
     * @ORM\ManyToOne(targetEntity=Competition::class, inversedBy="runs")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private Competition $competition;

    /**
     * @ORM\OneToOne(targetEntity=Location::class, inversedBy="run", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private Location $location;

    /**
     * @var Collection<int,Team> $teams
     * @ORM\ManyToMany(targetEntity=Team::class)
     * @Assert\Valid
     */
    private Collection $teams;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $noWinner = null;

    /**
     * @var Collection<int,Result> $scores
     * @ORM\ManyToMany(targetEntity=Result::class)
     * @Assert\Valid
     */
    private Collection $scores;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->scores = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(string $event): self
    {
        $this->event = $event;
        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function isFinish(): bool
    {
        $timezoneUTC = new \DateTimeZone('UTC');
        $currentDate = new \DateTime('now', $timezoneUTC);
        return ($currentDate > $this->endDate->setTimezone($timezoneUTC));
    }

    public function isOngoing(): bool
    {
        $timezoneUTC = new \DateTimeZone('UTC');
        $currentDate = new \DateTime('now', $timezoneUTC);
        return ($currentDate >= $this->startDate->setTimezone($timezoneUTC)
            && $currentDate <= $this->endDate->setTimezone($timezoneUTC));
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

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return Collection<int,Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            if (empty($this->competition)) {
                return $this;
            }
            if (empty($this->competition->getSport())) {
                return $this;
            }
            $maxTeams = $this->competition->getSport()->getMaxTeamsByRun();
            if ($maxTeams > 0 && count($this->teams) >= $maxTeams) {
                return $this;
            }
            $this->teams[] = $team;
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        $this->teams->removeElement($team);

        return $this;
    }

    public function getNoWinner(): ?bool
    {
        return $this->noWinner;
    }

    public function setNoWinner(?bool $noWinner): self
    {
        $this->noWinner = $noWinner;

        return $this;
    }

    /**
     * @return Collection<int,Result>
     */
    public function getScores(): Collection
    {
        return $this->scores;
    }

    public function addScore(Result $score): self
    {
        if (!$this->scores->contains($score)) {
            $this->scores[] = $score;
        }

        return $this;
    }

    public function removeScore(Result $score): self
    {
        $this->scores->removeElement($score);

        return $this;
    }
}
