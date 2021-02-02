<?php

namespace App\Form\Model;

use Closure;
use App\Entity\Run;
use App\Entity\Team;
use App\Entity\Member;
use App\Entity\BetCategory;
use App\DataConverter\OddsStorageInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class BettingRegistrationFormModel
{
    /**
     * @Assert\NotNull(
     *     message="Le montant du paris ne peut pas vide"
     * )
     * @Assert\PositiveOrZero(
     *     message="Le montant du paris ne peut pas être négatif"
     * )
     */
    private ?int $amount = null;

    /**
     * @var Team|Member|null $result
     * @Assert\Valid
     */
    private ?object $result = null;

    /* Result choice parameters */

    private string $categoryLabel = '';

    /**
     * @var Object[] $choices
     */
    private array $choices = [];

    private OddsStorageInterface $oddsStorageDataConverter;

    public function __construct(OddsStorageInterface $oddsStorageDataConverter)
    {
        $this->oddsStorageDataConverter = $oddsStorageDataConverter;
    }

    public function initializeObject(BetCategory $betCategoryEntity, Run $runEntity): void
    {
        $choicesType = $betCategoryEntity->getTarget() ?? '';
        $allowDraw = $betCategoryEntity->getAllowDraw() ?? false;
        $this->categoryLabel = $this->createCategoryLabel($betCategoryEntity);
        $this->choices = $this->createChoices($runEntity, $choicesType, $allowDraw);
    }

    private function createChoiceLabel(object $choice): string
    {
        $label = '';
        if ($choice instanceof Team) {
            $label = $choice->getName() ?? '';
        }
        if ($choice instanceof Member) {
            $team = $choice->getTeam();
            $teamName = ($team->getName() ?? '');
            $label = ($choice->getLastName() ?? '') . ' ' . ($choice->getFirstName() ?? '') . ' - ' . $teamName;
        }
        $odds = $choice->getOdds() ?? 0;
        $odds = $this->oddsStorageDataConverter->convertToOddsMultiplier($odds);
        $label = $odds . ' - ' . $label;
        return $label;
    }

    private function createCategoryLabel(BetCategory $betCategoryEntity): string
    {
        $betCategoryLabel = $betCategoryEntity->getName() ?? '';
        $firstLetter = mb_convert_case(mb_substr($betCategoryLabel, 0, 1), MB_CASE_UPPER);
        $betCategoryLabel = substr_replace($betCategoryLabel, $firstLetter, 0, 1);
        return $betCategoryLabel;
    }

    private function createDrawLabel(int $drawOdds, bool $teamType): string
    {
        if ($teamType === true) {
            $placeholder = "Nul";
        } else {
            $placeholder = "Aucun";
        }
        $placeholder = $this->oddsStorageDataConverter->convertToOddsMultiplier($drawOdds) . ' - ' . $placeholder;
        return $placeholder;
    }

    /** @param Object[] $choices by reference */
    private function createDrawChoice(array &$choices, bool $teamType, int $totalOdds): void
    {
        $choicesCount = count($choices);
        $drawOdds = (int)(round(($totalOdds / $choicesCount), 0, PHP_ROUND_HALF_UP));
        if ($teamType === true && $choicesCount === 2) {
            $last = $choices[1];
            $choices[1] = (object)["id" => null, "label" => $this->createDrawLabel($drawOdds, $teamType)];
            $choices[] = $last;
        } else {
            array_unshift(
                $choices,
                (object)["id" => null, "label" => $this->createDrawLabel($drawOdds, $teamType)]
            );
        }
    }

    /**
     * @param Object[] $choices by reference
     * @return int Sum of odds
     */
    public function createTeamChoices(array &$choices, Run $runEntity): int
    {
        $totalOdds = 0;
        $runTeams = $runEntity->getTeams();
        foreach ($runTeams as $team) {
            $choices[] = (object)["id" => $team->getId(), "label" => $this->createChoiceLabel($team)];
            $odds = $team->getOdds() ?? 0;
            $totalOdds += $odds;
        }
        return $totalOdds;
    }

    /**
     * @param Object[] $choices by reference
     * @return int Sum of odds
     */
    private function createMemberChoices(array &$choices, Run $runEntity): int
    {
        $totalOdds = 0;
        $runTeams = $runEntity->getTeams();
        $runMembers = [];
        foreach ($runTeams as $team) {
            $memberCollection = $team->getMembers();
            $runMembers = array_merge($runMembers, $memberCollection->toArray());
        }
        foreach ($runMembers as $member) {
            $choices[] = (object)["id" => $member->getId(), "label" => $this->createChoiceLabel($member)];
            $odds = $member->getOdds() ?? 0;
            $totalOdds += $odds;
        }
        return $totalOdds;
    }

    /** @return Object[] */
    private function createChoices(Run $runEntity, string $choicesType, bool $allowDraw): array
    {
        $choices = [];
        $totalOdds = 0;
        $teamType = false;
        if ($choicesType === BetCategory::TEAM_TYPE) {
            $teamType = true;
            $totalOdds = $this->createTeamChoices($choices, $runEntity);
        }
        if ($choicesType === BetCategory::MEMBER_TYPE) {
            $totalOdds = $this->createMemberChoices($choices, $runEntity);
        }
        if ($allowDraw === true) {
            $this->createDrawChoice($choices, $teamType, $totalOdds);
        }
        return $choices;
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

    /** @return Team|Member|null */
    public function getResult(): ?object
    {
        return $this->result;
    }

    /** @param Team|Member|null $result */
    public function setResult(?object $result): self
    {
        $this->result = $result;
        return $this;
    }

    /* Result choice parameters */

    public function getCategoryLabel(): string
    {
        return $this->categoryLabel;
    }

    public function setCategoryLabel(string $categoryLabel): self
    {
        $this->categoryLabel = $categoryLabel;
        return $this;
    }

    /** @return Object[] */
    public function getChoices(): array
    {
        return $this->choices;
    }

    /** @param Object[] $choices */
    public function setChoices(array $choices): self
    {
        $this->choices = $choices;
        return $this;
    }
}
