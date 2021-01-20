<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LocationRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LocationRepository::class)
 * @UniqueEntity(
 *     fields={"place", "country"},
 *     errorPath="place",
 *     message="Ce lieu est déjà enregistré."
 * )
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
     *     message="Le fuseau horaire du lieu ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Timezone(
     *     message="Le fuseau horaire du lieu {{ value }} n'est pas valide"
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
}
