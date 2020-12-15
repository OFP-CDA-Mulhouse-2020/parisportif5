<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\MemberRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MemberRepository::class)
 * @ORM\Table(name="`member`")
 */
class Member
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
     *     message="Le nom de famille ne peut être vide",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\'\s]+$/u",
     *     message="Seuls les lettres, traits d'union et apostrophes sont autorisés"
     * )
     */
    private string $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le prénom ne peut être vide",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/^[\p{L}\-\s]+$/u",
     *     message="Seuls les lettres et traits d'union sont autorisés"
     * )
     */
    private string $firstName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }
}
