<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LocationRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LocationRepository::class)
 */
class Location
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
     *     message="Le lieu ne peut pas être vide",
     *     normalizer="trim"
     * )
     */
    private string $place;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le fuseau horaire ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Timezone(
     *     message="Le fuseau horaire {{ value }} n'est pas valide"
     * )
     */
    private string $timeZone;

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
     * @ORM\OneToOne(targetEntity=Run::class, mappedBy="location", cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private Run $run;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): self
    {
        $this->place = $place;
        return $this;
    }

    public function getTimeZone(): ?string
    {
        return $this->timeZone;
    }

    public function setTimeZone(string $timeZone): self
    {
        $this->timeZone = $timeZone;
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

    public function getRun(): ?Run
    {
        return $this->run;
    }

    public function setRun(Run $run): self
    {
        $this->run = $run;

        // set the owning side of the relation if necessary
        if ($run->getLocation() !== $this) {
            $run->setLocation($this);
        }

        return $this;
    }
}
