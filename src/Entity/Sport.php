<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SportRepository;

/**
 * @ORM\Entity(repositoryClass=SportRepository::class)
 * @UniqueEntity(
 *     fields={"name", "country"},
 *     errorPath="name",
 *     message="Ce sport est déjà enregistré."
 * )
 * @Assert\Expression(
 *      "this.getIndividualType() or this.getCollectiveType()",
 *      message="Le sport ne peut être ni individuel ni collectif et doit être au moins l'un des deux"
 * )
 */
class Sport
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
     *     message="Le nom du sport ne peut être vide",
     *     normalizer="trim"
     * )
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le pays doit être renseigné",
     *     normalizer="trim"
     * )
     * @Assert\Country(
     *     message="Le pays {{ value }} n'est pas valide",
     * )
     */
    private string $country;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(
     *     choices=Sport::RUN_TYPES,
     *     message="Choisisez un type de résultat valide"
     * )
     */
    private string $runType;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $individualType;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $collectiveType;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(
     *     message="Le nombre d'équipes minimum doit être positif ou égal à zéro"
     * )
     */
    private int $minTeamsByRun = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Positive(
     *     message="Le nombre d'équipes maxinum doit être positif"
     * )
     * @Assert\GreaterThanOrEqual(
     *     propertyPath="minTeamsByRun",
     *     message="Le nombre d'équipes doit être supérieur ou égal au nombre minimum"
     * )
     */
    private ?int $maxTeamsByRun;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(
     *     message="Le nombre de compétiteurs minimum doit être positif ou égal à zéro"
     * )
     */
    private int $minMembersByTeam = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Positive(
     *     message="Le nombre de compétiteurs maxinum doit être positif"
     * )
     * @Assert\GreaterThanOrEqual(
     *     propertyPath="minMembersByTeam",
     *     message="Le nombre de compétiteurs doit être supérieur ou égal au nombre minimum"
     * )
     */
    private ?int $maxMembersByTeam;

    /** @const string FIXTURE_TYPE */
    public const FIXTURE_TYPE = "fixture";

    /** @const string RACE_TYPE*/
    public const RACE_TYPE = "race";

    /**
     * @const string[] RUN_TYPES
    */
    public const RUN_TYPES = [self::FIXTURE_TYPE, self::RACE_TYPE];

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

    public function getMaxMembersByTeam(): ?int
    {
        return $this->maxMembersByTeam;
    }

    public function setMaxMembersByTeam(?int $maxMembersByTeam): self
    {
        $this->maxMembersByTeam = $maxMembersByTeam;

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

    public function getRunType(): ?string
    {
        return $this->runType;
    }

    public function setRunType(string $runType): self
    {
        $this->runType = $runType;

        return $this;
    }

    public function getIndividualType(): ?bool
    {
        return $this->individualType;
    }

    public function setIndividualType(bool $individualType): self
    {
        $this->individualType = $individualType;

        return $this;
    }

    public function getCollectiveType(): ?bool
    {
        return $this->collectiveType;
    }

    public function setCollectiveType(bool $collectiveType): self
    {
        $this->collectiveType = $collectiveType;

        return $this;
    }

    public function getMaxTeamsByRun(): ?int
    {
        return $this->maxTeamsByRun;
    }

    public function setMaxTeamsByRun(?int $maxTeamsByRun): self
    {
        $this->maxTeamsByRun = $maxTeamsByRun;

        return $this;
    }

    public function getMinMembersByTeam(): ?int
    {
        return $this->minMembersByTeam;
    }

    public function setMinMembersByTeam(int $minMembersByTeam): self
    {
        $this->minMembersByTeam = $minMembersByTeam;

        return $this;
    }

    public function getMinTeamsByRun(): ?int
    {
        return $this->minTeamsByRun;
    }

    public function setMinTeamsByRun(int $minTeamsByRun): self
    {
        $this->minTeamsByRun = $minTeamsByRun;

        return $this;
    }
}
