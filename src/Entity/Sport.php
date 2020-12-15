<?php

namespace App\Entity;

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
    private int $numberOfCompetitors;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le pays doit être renseigné",
     *     normalizer="trim"
     * )
     * @Assert\Country
     */
    private string $country;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank,
     *     normalizer="trim"
     * @Assert\Choice({"fixture", "race"})
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

    public function getNumberOfCompetitors(): ?int
    {
        return $this->numberOfCompetitors;
    }

    public function setNumberOfCompetitors(int $numberOfCompetitors): self
    {
        $this->numberOfCompetitors = $numberOfCompetitors;

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
}
