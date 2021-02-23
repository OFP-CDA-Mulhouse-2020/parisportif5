<?php

namespace App\Form\Model;

use App\Entity\Bet;
use App\Validator\UniqueBet;
use Symfony\Component\Validator\Constraints as Assert;

final class BettingRegistrationFormModel
{
    /**
     * @Assert\NotNull(
     *     message="Le montant du paris ne peut pas être vide."
     * )
     * @Assert\PositiveOrZero(
     *     message="Le montant du paris ne peut pas être négatif."
     * )
     */
    private ?int $amount = null;

    /**
     * @Assert\NotNull(
     *     message="Le résultat du paris ne peut pas être vide."
     * )
     */
    private object $result;

    private string $categoryLabel = '';

    private int $categoryId;

    /**
     * @var Object[] $choices
     */
    private array $choices = [];

    /**
     * @Assert\NotNull(
     *     message="La date de soumission ne peut pas être vide."
     * )
     * @UniqueBet()
     */
    private ?\DateTimeImmutable $submitDate = null;

    /** @param Object[] $choices */
    public function __construct(array $choices, string $categoryLabel, int $categoryId)
    {
        $this->choices = $choices;
        $this->categoryId = $categoryId;
        $this->categoryLabel = $categoryLabel;
    }

    public function getDateByTenthOfSecond(\DateTimeImmutable $date): \DateTimeImmutable
    {
        $timeInArray = explode(':', $date->format('H:i:s'));
        $modulo = (int)$timeInArray[2] % 10;
        if ($modulo !== 0) {
            $timeInArray[2] = (int)$timeInArray[2] - $modulo;
        }
        $date = $date->setTime((int)$timeInArray[0], (int)$timeInArray[1], (int)$timeInArray[2]);
        return $date;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;
        return $this;
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

    public function setSubmitDate(): self
    {
        $date = new \DateTimeImmutable(
            "now",
            new \DateTimeZone(Bet::STORED_TIME_ZONE)
        );
        $this->submitDate = $this->getDateByTenthOfSecond($date);
        return $this;
    }

    public function getSubmitDate(): ?\DateTimeImmutable
    {
        return $this->submitDate;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }
}
