<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BetCategoryRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
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
 * @UniqueEntity(
 *     fields="name",
 *     message="Cette catégorie de paris est déjà enregistré."
 * )
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
     *     message="Le nom de la catégorie de paris ne peut pas être vide",
     *     normalizer="trim"
     * )
     * @Assert\Regex(
     *     pattern="/^(?<![\_\-])([a-z]([\_\-][a-z])?)+(?![\_\-])$/i",
     *     message="Le nom de la catégorie de paris n'accepte que des lettres sans accents, le tiret et le underscore"
     * )
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $allowDraw;

    /**
     * @ORM\Column(type="string", length=7)
     * @Assert\NotBlank(
     *     message="Le type de cible ne peut pas être vide.",
     *     normalizer="trim"
     * )
     * @Assert\Choice(
     *     choices=Billing::TARGET_TYPES,
     *     message="Choisisez une cible valide."
     * )
     */
    private string $target;

    /**
     * @const string[] TARGET_TYPES
    */
    public const TARGET_TYPES = ["teams", "members"];

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getAllowDraw(): ?bool
    {
        return $this->allowDraw;
    }

    public function setAllowDraw(bool $allowDraw): self
    {
        $this->allowDraw = $allowDraw;

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
}
