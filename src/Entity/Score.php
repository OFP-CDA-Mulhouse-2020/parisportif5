<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ScoreRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ScoreRepository::class)
 */
class Score
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank,
     *     normalizer="trim"
     * @Assert\Choice({"fixture", "race"})
     */
    private string $runType;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero
     */
    private int $value;
    //la valeur des scores de temps est exprimée en millièmes de seconde

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRunType(): ?string
    {
        return $this->runType;
    }

    public function setRunType(string $runType): self
    {
        $this->runType = $runType;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }
}
