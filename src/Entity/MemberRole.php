<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\MemberRoleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MemberRoleRepository::class)
 */
class MemberRole
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
     *     normalizer="trim",
     *     message="Le rôle du membre ne peut pas être vide"
     * )
     * @Assert\Regex(
     *     pattern="/^(?<![\_\-])([a-z]([\_\-][a-z])?)+(?![\_\-])$/i",
     *     message="Le rôle du membre n'accepte que des lettres sans accents, le tiret et le underscore"
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
}
