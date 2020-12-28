<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\SportRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SportRepository::class)
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
     * @ORM\Column(type="integer")
     * @Assert\NotNull(
     *     message="Le nombre de compétiteurs ne peut être vide"
     * )
     * @Assert\GreaterThanOrEqual(1)(
     *     message="Le nombre de compétiteurs doit être supérieur ou égal à 1"
     * )
     */
    private int $maxMembersByTeam;

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
     * @Assert\NotBlank,
     *     normalizer="trim"
     * @Assert\GreaterThanOrEqual(1)(
     *     message="Le nombre d'équipes doit être supérieur ou égal à 1"
     * )
     */
    private int $maxTeamsByRun;

    public const RUN_TYPES = ["fixture", "race"];

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

    public function setMaxMembersByTeam(int $maxMembersByTeam): self
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

    public function setMaxTeamsByRun(int $maxTeamsByRun): self
    {
        $this->maxTeamsByRun = $maxTeamsByRun;

        return $this;
    }
}
