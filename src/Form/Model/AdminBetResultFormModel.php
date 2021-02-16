<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class AdminBetResultFormModel
{
    /**
     * @Assert\NotNull(
     *     message="Le résultat du paris ne peut pas être vide."
     * )
     */
    private object $result;

    private string $categoryLabel = '';

    private int $categoryId;

    /** @var Object[] $choices */
    private array $choices = [];

    /** @param Object[] $choices */
    public function __construct(array $choices, string $categoryLabel, int $categoryId)
    {
        $this->choices = $choices;
        $this->categoryId = $categoryId;
        $this->categoryLabel = $categoryLabel;
    }

    public function getResult(): object
    {
        return $this->result;
    }

    public function setResult(object $result): self
    {
        $this->result = $result;
        return $this;
    }

    public function getCategoryLabel(): string
    {
        return $this->categoryLabel;
    }

    /** @return Object[] */
    public function getChoices(): array
    {
        return $this->choices;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }
}
