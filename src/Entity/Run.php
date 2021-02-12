<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RunRepository;

/**
 * @ORM\Entity(repositoryClass=RunRepository::class)
 * @UniqueEntity(
 *     fields={"name", "event", "startDate", "competition", "location"},
 *     errorPath="name",
 *     message="Cette rencontre ou course est déjà enregistrée."
 * )
 */
class Run extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

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
     * @ORM\ManyToOne(targetEntity=Competition::class, inversedBy="runs")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private Competition $competition;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class)
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

    public function __construct()
    {
        $this->teams = new ArrayCollection();
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

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $startDate = $this->convertedToStoreDateTime($startDate);
        $this->startDate = $startDate;
        return $this;
    }

    public function canBet(): bool
    {
        $timeZoneUTC = new \DateTimeZone(self::STORED_TIME_ZONE);
        $currentDate = new \DateTime('now', $timeZoneUTC);
        return ($currentDate < $this->startDate->setTimezone($timeZoneUTC));
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

    /** @return BetCategory[] */
    public function getBetCategories(): array
    {
        if (!empty($this->competition) === true) {
            return $this->competition->getBetCategoriesForRun();
        }
        return [];
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

    public function getTeamsCounts(): int
    {
        return $this->teams->count();
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
            $maxTeams = $this->competition->getSport()->getMaxTeamsByRun() ?? 0;
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

    /**
     * @Assert\IsTrue(
     *     message="Le nombre requis d'équipe n'est pas atteint ou est dépassé"
     * )
     */
    public function hasRequiredNumberOfTeams(): bool
    {
        $teamsCount = $this->getTeams()->count();
        $minTeams = 0;
        if (!empty($this->competition)) {
            if (!empty($this->competition->getSport())) {
                $minTeams = $this->competition->getSport()->getMinTeamsByRun();
            }
        }
        $maxTeams = 0;
        if (!empty($this->competition)) {
            if (!empty($this->competition->getSport())) {
                $maxTeams = $this->competition->getSport()->getMaxTeamsByRun() ?? $minTeams;
            }
        }
        return ($minTeams === 0 && $maxTeams === 0) ?:
            ($minTeams <= $teamsCount && $maxTeams >= $teamsCount);
    }

    public function hasTeams(): bool
    {
        return !$this->teams->isEmpty();
    }

    public function getTeamsCount(): int
    {
        return $this->teams->count();
    }

    public function __toString(): string
    {
        return $this->id . ' - ' . $this->name . ' (' . $this->event . ')';
    }
}
