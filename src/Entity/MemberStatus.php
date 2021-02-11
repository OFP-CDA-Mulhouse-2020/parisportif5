<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MemberStatusRepository;

/**
 * @ORM\Entity(repositoryClass=MemberStatusRepository::class)
 * @UniqueEntity(
 *     fields="name",
 *     message="Ce statut de membre est déjà enregistré."
 * )
 */
class MemberStatus extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     normalizer="trim",
     *     message="Le statut du membre ne peut pas être vide"
     * )
     * @Assert\Regex(
     *     pattern="/^(?<![\_\-])([a-z]([\_\-][a-z])?)+(?![\_\-])$/i",
     *     message="Le statut du membre n'accepte que des lettres sans accents, le tiret et le underscore"
     * )
     */
    private string $name;

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

    public function __toString(): string
    {
        return $this->id . ' - ' . $this->name;
    }
}
