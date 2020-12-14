<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
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
     * @Assert\NotBlank(
     *     message="Le nom du sport ne peut être vide"
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
     *     message="Le pays doit être renseigné"     *
     * )
     * @Assert\Country
     */
    private string $country;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Choice({"fixture", "race"})
     */
    private string $runType;

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
}
