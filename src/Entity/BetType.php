<?php

namespace App\Entity;

use App\Repository\BetTypeRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BetTypeRepository::class)
 */
class BetType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le sujet du paris ne doit pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Choice(
     *     choices=BetType::TARGETS,
     *     message="Choisissez un sujet de paris valide"
     * )
     */
    private string $target;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $active;

    /** @const array SUBJECTS */
    public const TARGETS = ["run", "competition", "team", "member"];

    public function __construct()
    {
        $this->description = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(string $target): self
    {
        $this->target = $target;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function desactivate(): void
    {
        $this->active = false;
    }
}
