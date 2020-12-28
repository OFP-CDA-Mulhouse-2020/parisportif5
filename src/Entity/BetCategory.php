<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BetCategoryRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/*// liste des catégories :
    Foot :
        résultat => result
        butteur (membre) => score
        nombre de but => goals Line
        score => points
        mi-temps la plus prolifique => most prolific half-time
    Handball :
        résultat => result
        butteur (membre) => (player) ToScore
        nombre de but => goals Line
        mi-temps la plus prolifique => most prolific half-time
    Formule 1 :
        résultat => result
        podium (top 3)
        points (top10)
        termine la course => finish the race
    Tennis :
        résultat => result
        atteindre la finale => reach the final
        nombre de sets
    Tennis de table :
        résultat => result
        atteindre la finale => reach the final
        nombre de sets
    */

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
