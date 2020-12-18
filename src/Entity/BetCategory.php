<?php

namespace App\Entity;

use App\Repository\BetCategoryRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BetCategoryRepository::class)
 */
class BetCategory
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
     *     message="Le titre de la catégorie de paris ne peut pas être vide",
     *     normalizer="trim"
     * )
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description;

    /**
     * @var array<string> $items
     * @ORM\Column(type="array")
     * @Assert\Count(
     *      min = 1,
     *      minMessage = "Vous devez indiquer au moins un élément dans la catégorie de paris",
     * )
     */
    private array $items = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
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

    /** @return array<string> */
    public function getItems(): ?array
    {
        return $this->items;
    }

    /** @param array<string> $items */
    public function setItems(array $items): self
    {
        $this->items = $items;
        return $this;
    }
}
