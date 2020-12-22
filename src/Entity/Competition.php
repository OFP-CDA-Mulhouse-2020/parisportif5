<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CompetitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompetitionRepository::class)
 */
class Competition
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
     *     message="Le nom de la compétition ne peut pas être vide",
     *     normalizer="trim"
     * )
     */
    private string $name;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\GreaterThanOrEqual(
     *     value="tomorrow UTC",
     *     message="La date du début de la compétition doit être supérieur ou égale au {{ compared_value }} UTC"
     * )
     */
    private \DateTimeImmutable $startDate;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\GreaterThan(
     *     propertyPath="startDate",
     *     message="La date de fin de la compétition doit être supérieur à la date du début de celle-ci"
     * )
     */
    private \DateTimeImmutable $endDate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le pays ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Country(
     *     message="Le pays {{ value }} n'est pas valide",
     * )
     */
    private string $country;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive(
     *     message="Le nombre maximum de course ou de rencontre (Run) doit être un entier positif"
     * )
     */
    private int $maxRuns;

    /**
     * @var Collection<int,Team> $winners
     * @ORM\ManyToMany(targetEntity=Team::class)
     * @Assert\Valid
     * @Assert\Count(
     *      max = 3,
     *      maxMessage = "Vous ne pouvez pas ajouter plus de {{ limit }} gagnants"
     * )
     */
    private Collection $winners;

    /**
     * @var Collection<int,Run> $runs
     * @ORM\OneToMany(targetEntity=Run::class, mappedBy="competition", orphanRemoval=true)
     * @Assert\Valid
     */
    private Collection $runs;

    /**
     * @ORM\OneToOne(targetEntity=Sport::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private Sport $sport;

    public function __construct()
    {
        $this->winners = new ArrayCollection();
        $this->runs = new ArrayCollection();
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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getMaxRuns(): ?int
    {
        return $this->maxRuns;
    }

    public function setMaxRuns(int $maxRuns): self
    {
        $this->maxRuns = $maxRuns;
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

    /**
     * @return Collection<int,Team>
     */
    public function getWinners(): Collection
    {
        return $this->winners;
    }

    public function addWinner(Team $winner): self
    {
        if (!$this->winners->contains($winner)) {
            $this->winners[] = $winner;
        }
        return $this;
    }

    public function removeWinner(Team $winner): self
    {
        $this->winners->removeElement($winner);
        return $this;
    }

    /*
     * @Assert\IsFalse(
     *     message="Le nombre maximum de course ou de rencontre (Run) a été atteint"
     * )
     *
    public function isOverMaxRuns(): bool
    {
        if (isset($this->maxRuns)) {
            if ($this->maxRuns > 0 && count($this->runs) >= $this->maxRuns) {
                return true;
            }
        }
        return false;
    }*/

    /**
     * @return Collection<int,Run>
     */
    public function getRuns(): Collection
    {
        return $this->runs;
    }

    public function addRun(Run $run): self
    {
        if (!$this->runs->contains($run)) {
            if (!isset($this->maxRuns)) {
                return $this;
            }
            if ($this->maxRuns > 0 && count($this->runs) >= $this->maxRuns) {
                return $this;
            }
            $this->runs[] = $run;
            $run->setCompetition($this);
        }

        return $this;
    }

    public function removeRun(Run $run): self
    {
        if ($this->runs->removeElement($run)) {
            // set the owning side to null (unless already changed)
            /*if ($run->getCompetition() === $this) {
                $run->setCompetition(null);
            }*/
        }

        return $this;
    }

    public function getSport(): ?Sport
    {
        return $this->sport;
    }

    public function setSport(Sport $sport): self
    {
        $this->sport = $sport;
        return $this;
    }
}
