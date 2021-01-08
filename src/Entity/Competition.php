<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CompetitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompetitionRepository::class)
 * @UniqueEntity(
 *     fields={"name", "startDate", "endDate", "country"},
 *     errorPath="name",
 *     message="Cette compétition est déjà enregistrée."
 * )
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
     * @Assert\PositiveOrZero(
     *     message="Le nombre minimum de course ou de rencontre (Run) doit être positif ou égal à zéro"
     * )
     */
    private int $minRuns = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Positive(
     *     message="Le nombre maximum de course ou de rencontre (Run) doit être positif"
     * )
     * @Assert\GreaterThanOrEqual(
     *     propertyPath="minRuns",
     *     message="Le nombre de course ou de rencontre (Run) doit être supérieur ou égal au nombre minimum"
     * )
     */
    private ?int $maxRuns;

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

    /**
     * @var Collection<int,BetCategory> $betCategories
     * @ORM\ManyToMany(targetEntity=BetCategory::class)
     * @Assert\Count(
     *      min = 1,
     *      minMessage = "Vous devez ajouter au moins {{ limit }} un objet BetCategory",
     * )
     * @Assert\Valid
     */
    private Collection $betCategories;

    /**
     * @ORM\OneToOne(targetEntity=Result::class, cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private ?Result $result = null;

    public function __construct()
    {
        $this->runs = new ArrayCollection();
        $this->betCategories = new ArrayCollection();
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

    public function setMaxRuns(?int $maxRuns): self
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
     * @return Collection<int,Run>
     */
    public function getRuns(): Collection
    {
        return $this->runs;
    }

    public function addRun(Run $run): self
    {
        if (!$this->runs->contains($run)) {
            $maxRuns = $this->maxRuns ?? 0;
            if ($maxRuns > 0 && count($this->runs) >= $maxRuns) {
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

    /**
     * @return Collection<int,BetCategory>
     */
    public function getBetCategories(): Collection
    {
        return $this->betCategories;
    }

    public function addBetCategory(BetCategory $betCategory): self
    {
        if (!$this->betCategories->contains($betCategory)) {
            $this->betCategories[] = $betCategory;
        }

        return $this;
    }

    public function removeBetCategory(BetCategory $betCategory): self
    {
        $this->betCategories->removeElement($betCategory);

        return $this;
    }

    public function getResult(): ?Result
    {
        return $this->result;
    }

    public function setResult(?Result $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function hasResult(): bool
    {
        return isset($this->result) ? true : false;
    }

    public function getMinRuns(): ?int
    {
        return $this->minRuns;
    }

    public function setMinRuns(int $minRuns): self
    {
        $this->minRuns = $minRuns;

        return $this;
    }

    /**
     * @Assert\IsTrue(
     *     message="Le nombre de course ou de rencontre (Run) n'est pas atteint ou est dépassé"
     * )
     */
    public function hasRequiredNumberOfRuns(): bool
    {
        $runsCount = $this->getRuns()->count();
        $minRuns = $this->getMinRuns();
        $maxRuns = $this->getMaxRuns() ?? $minRuns;
        return ($minRuns == 0 && $maxRuns == 0) ?:
            ($minRuns <= $runsCount && $maxRuns >= $runsCount);
    }
}
