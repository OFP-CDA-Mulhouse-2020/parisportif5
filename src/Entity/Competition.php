<?php

namespace App\Entity;

use App\Repository\CompetitionRepository;
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
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan(
     *     "today UTC",
     *     message="La date de début de la compétition doit être supérieur ou égale au {{ compared_value }} UTC"
     * )
     */
    private \DateTimeInterface $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan(
     *     propertyPath="startDate",
     *     message="La date de fin de la compétition doit être supérieur à la date du début de celle-ci ({{ compared_value }} UTC)"
     * )
     */
    private \DateTimeInterface $endDate;

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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
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

    public function isFinish(): bool
    {
        $currentDate = new \DateTime('now', new \DateTimeZone('UTC'));
        $endDate = \DateTime::createFromFormat('Y-m-d H:i:s u', $this->endDate->format('Y-m-d H:i:s u'), new \DateTimeZone('UTC'));
        return ($currentDate > $endDate);
    }

    public function isOngoing(): bool
    {
        $currentDate = new \DateTime('now', new \DateTimeZone('UTC'));
        $startDate = \DateTime::createFromFormat('Y-m-d H:i:s u', $this->startDate->format('Y-m-d H:i:s u'), new \DateTimeZone('UTC'));
        $endDate = \DateTime::createFromFormat('Y-m-d H:i:s u', $this->endDate->format('Y-m-d H:i:s u'), new \DateTimeZone('UTC'));
        return ($currentDate >= $startDate && $currentDate <= $endDate);
    }
}
