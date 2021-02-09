<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BetSavedRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BetSavedRepository::class)
 */
class BetSaved extends AbstractEntity
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
     * @ORM\Column(type="integer")
     * Les gains du paris en centimes
     */
    private int $gains;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isWinning = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $betDate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le nom de la catégorie de paris ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/^(?<![\_\-])([a-z]([\_\-][a-z])?)+(?![\_\-])$/i",
     *     message="Le nom de la catégorie de paris n'accepte que des lettres sans accents, le tiret et le underscore"
     * )
     */
    private string $betCategoryName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le nom de la compétition ne peut pas être vide",
     *     normalizer="trim"
     * )
     */
    private string $competitionName;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\LessThanOrEqual(
     *     value="tomorrow UTC",
     *     message="La date du début de la compétition doit être inférieur ou égale au {{ compared_value }} UTC"
     * )
     */
    private \DateTimeImmutable $competitionStartDate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le pays de la compétition ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Country(
     *     message="Le pays de la compétition {{ value }} n'est pas valide",
     * )
     */
    private string $competitionCountry;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le nom du sport de la compétition ne peut être vide",
     *     normalizer="trim"
     * )
     */
    private string $competitionSportName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le pays du sport de la compétition doit être renseigné",
     *     normalizer="trim"
     * )
     * @Assert\Country(
     *     message="Le pays du sport de la compétition {{ value }} n'est pas valide",
     * )
     */
    private string $competitionSportCountry;

       /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     message="Le nom de la course ou du match ne peut pas être vide",
     *     normalizer="trim",
     *     allowNull=true
     * )
     */
    private ?string $runName = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     message="Le nom de l'évènement de la course ou du match ne peut pas être vide",
     *     normalizer="trim",
     *     allowNull=true
     * )
     */
    private ?string $runEvent = null;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Assert\LessThanOrEqual(
     *     value="tomorrow UTC",
     *     message="La date du début de la course ou du match doit être inférieur ou égale au {{ compared_value }} UTC"
     * )
     */
    private ?\DateTimeImmutable $runStartDate = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     normalizer="trim",
     *     message="Le nom de l'équipe ne peut pas être vide",
     *     allowNull=true
     * )
     * @Assert\Regex(
     *     pattern="/^\p{L}((?<!\')\'|\p{L}?\s?(?!\s)(\.(?!\.))?|\-(?!\-)|\s(?!\s)|\d)+\S$/u",
     *     message="Seules les chiffres, les lettres, les tirets, les apostrophes et les points sont autorisés pour le nom de l'équipe"
     * )
     * @Assert\Length(
     *     min=2,
     *     minMessage="Le nom de l'équipe doit avoir au moins {{ limit }} caractères",
     * )
     */
    private ?string $teamName = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     message="Le pays de l'équipe doit être renseigné",
     *     normalizer="trim",
     *     allowNull=true
     * )
     * @Assert\Country(
     *     message="Le pays de l'équipe {{ value }} n'est pas valide",
     * )
     */
    private ?string $teamCountry = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     message="Le nom de famille du membre ne peut être vide",
     *     normalizer="trim",
     *     allowNull=true
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\'\s]+$/u",
     *     message="Seuls les lettres, traits d'union et apostrophes sont autorisés pour le nom du membre"
     * )
     */
    private ?string $memberLastName = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     message="Le prénom du membre ne peut être vide",
     *     normalizer="trim",
     *     allowNull=true
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\s]+$/u",
     *     message="Seuls les lettres et traits d'union sont autorisés pour le prénom du membre"
     * )
     */
    private ?string $memberFirstName = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *     message="Le pays du membre doit être renseigné",
     *     normalizer="trim",
     *     allowNull=true
     * )
     * @Assert\Country(
     *     message="Le pays du membre {{ value }} n'est pas valide",
     * )
     */
    private ?string $memberCountry = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private User $user;

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

    public function getGains(): ?int
    {
        return $this->gains;
    }

    public function setGains(int $gains): self
    {
        $this->gains = $gains;
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

    public function isWinning(): ?bool
    {
        return $this->isWinning;
    }

    public function getBetCategoryName(): ?string
    {
        return $this->betCategoryName;
    }

    public function setBetCategoryName(string $betCategoryName): self
    {
        $this->betCategoryName = $betCategoryName;
        return $this;
    }

    public function getCompetitionName(): ?string
    {
        return $this->competitionName;
    }

    public function setCompetitionName(string $competitionName): self
    {
        $this->competitionName = $competitionName;
        return $this;
    }

    public function getCompetitionStartDate(): ?\DateTimeImmutable
    {
        return $this->competitionStartDate;
    }

    public function setCompetitionStartDate(\DateTimeInterface $competitionStartDate): self
    {
        $competitionStartDate = $this->convertedToStoreDateTime($competitionStartDate);
        $this->competitionStartDate = $competitionStartDate;
        return $this;
    }

    public function getCompetitionCountry(): ?string
    {
        return $this->competitionCountry;
    }

    public function setCompetitionCountry(string $competitionCountry): self
    {
        $this->competitionCountry = $competitionCountry;
        return $this;
    }

    public function getCompetitionSportName(): ?string
    {
        return $this->competitionSportName;
    }

    public function setCompetitionSportName(string $competitionSportName): self
    {
        $this->competitionSportName = $competitionSportName;
        return $this;
    }

    public function getCompetitionSportCountry(): ?string
    {
        return $this->competitionSportCountry;
    }

    public function setCompetitionSportCountry(string $competitionSportCountry): self
    {
        $this->competitionSportCountry = $competitionSportCountry;
        return $this;
    }

    public function getRunName(): ?string
    {
        return $this->runName;
    }

    public function setRunName(string $runName): self
    {
        $this->runName = $runName;
        return $this;
    }

    public function getRunEvent(): ?string
    {
        return $this->runEvent;
    }

    public function setRunEvent(string $runEvent): self
    {
        $this->runEvent = $runEvent;
        return $this;
    }

    public function getRunStartDate(): ?\DateTimeImmutable
    {
        return $this->runStartDate;
    }

    public function setRunStartDate(\DateTimeInterface $runStartDate): self
    {
        $runStartDate = $this->convertedToStoreDateTime($runStartDate);
        $this->runStartDate = $runStartDate;
        return $this;
    }

    public function getTeamName(): ?string
    {
        return $this->teamName;
    }

    public function setTeamName(?string $teamName): self
    {
        $this->teamName = $teamName;
        return $this;
    }

    public function getTeamCountry(): ?string
    {
        return $this->teamCountry;
    }

    public function setTeamCountry(string $teamCountry): self
    {
        $this->teamCountry = $teamCountry;
        return $this;
    }

    public function getMemberLastName(): ?string
    {
        return $this->memberLastName;
    }

    public function setMemberLastName(string $memberLastName): self
    {
        $this->memberLastName = $memberLastName;
        return $this;
    }

    public function getMemberFirstName(): ?string
    {
        return $this->memberFirstName;
    }

    public function setMemberFirstName(string $memberFirstName): self
    {
        $this->memberFirstName = $memberFirstName;
        return $this;
    }

    public function getMemberFullName(): string
    {
        $fullName = trim(($this->memberFirstName ?? '') . ' ' . ($this->memberLastName ?? ''));
        return !empty($fullName) ? $fullName : '';
    }

    public function getMemberCountry(): ?string
    {
        return $this->memberCountry;
    }

    public function setMemberCountry(string $memberCountry): self
    {
        $this->memberCountry = $memberCountry;
        return $this;
    }

    public function getTargetName(): string
    {
        return $this->runName ?? $this->competitionName;
    }

    public function getSelectName(): ?string
    {
        return $this->getMemberFullName() ?? $this->teamName;
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
}
