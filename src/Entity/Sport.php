<?php

namespace App\Entity;

use App\Repository\SportRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SportRepository::class)
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
     */
    private string $name;

    /**
     * @ORM\Column(type="integer")
     */
    private int $numberOfCompetitors;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $collectiveType;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $individualType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $runType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $country;

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

    public function getCollectiveType(): ?bool
    {
        return $this->collectiveType;
    }

    public function setCollectiveType(bool $collectiveType): self
    {
        $this->collectiveType = $collectiveType;

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

    public function getRunType(): ?string
    {
        return $this->runType;
    }

    public function setRunType(string $runType): self
    {
        $this->runType = $runType;

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
}
