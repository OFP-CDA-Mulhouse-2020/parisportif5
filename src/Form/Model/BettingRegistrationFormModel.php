<?php

namespace App\Form\Model;

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

    private function createChoiceLabel(object $choice, string $odds): string
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

    private function createDrawLabel(string $drawOdds, bool $teamType): string
    {
        if ($teamType === true) {
            $placeholder = "Nul";
        } else {
            $placeholder = "Aucun";
        }
        $placeholder = $drawOdds . ' - ' . $placeholder;
        return $placeholder;
    }

    /** @param Object[] $choices by reference */
    private function createDrawChoice(array &$choices, bool $teamType, int $totalOdds): void
    {
        $choicesCount = count($choices);
        $drawOdds = (int)(round(($totalOdds / $choicesCount), 0, PHP_ROUND_HALF_UP));
        $drawOdds = $this->oddsStorageDataConverter->convertToOddsMultiplier($drawOdds);
        if ($teamType === true && $choicesCount === 2) {
            $last = $choices[1];
            $choices[1] = (object)["id" => 0,
                "label" => $this->createDrawLabel((string)$drawOdds, $teamType),
                "className" => '',
                "odds" => $drawOdds
            ];
            $choices[] = $last;
        } else {
            array_unshift(
                $choices,
                (object)["id" => 0,
                    "label" => $this->createDrawLabel((string)$drawOdds, $teamType),
                    "className" => '',
                    "odds" => $drawOdds
                ]
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
            $odds = $team->getOdds() ?? 0;
            $odds = $this->oddsStorageDataConverter->convertToOddsMultiplier($odds);
            $choices[] = (object)["id" => $team->getId(),
                "label" => $this->createChoiceLabel($team, (string)$odds),
                "className" => Team::class,
                "odds" => $odds
            ];
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
            $odds = $member->getOdds() ?? 0;
            $odds = $this->oddsStorageDataConverter->convertToOddsMultiplier($odds);
            $choices[] = (object)["id" => $member->getId(),
                "label" => $this->createChoiceLabel($member, (string)$odds),
                "className" => Member::class,
                "odds" => $odds
            ];
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
}
