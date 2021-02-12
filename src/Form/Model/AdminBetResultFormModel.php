<?php

namespace App\Form\Model;

use App\Entity\Run;
use App\Entity\Team;
use App\Entity\Member;
use App\Entity\BetCategory;
use App\Entity\Competition;
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

    /**
     * @var Object[] $choices
     */
    private array $choices = [];

    public function initializeWithRun(BetCategory $betCategoryEntity, Run $runEntity): void
    {
        $this->initializeCategoryLabel($betCategoryEntity);
        $runTeams = $runEntity->getTeams()->toArray();
        $this->choices = $this->createChoices($runTeams, $betCategoryEntity);
    }

    public function initializeWithCompetition(BetCategory $betCategoryEntity, Competition $competitionEntity): void
    {
        $this->initializeCategoryLabel($betCategoryEntity);
        $competitionTeams = $competitionEntity->getTeams();
        $this->choices = $this->createChoices($competitionTeams, $betCategoryEntity);
    }

    private function initializeCategoryLabel(BetCategory $betCategoryEntity): void
    {
        $this->categoryLabel = $this->createCategoryLabel($betCategoryEntity);
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
        return $label;
    }

    private function createCategoryLabel(BetCategory $betCategoryEntity): string
    {
        $betCategoryLabel = $betCategoryEntity->getName() ?? '';
        $firstLetter = mb_convert_case(mb_substr($betCategoryLabel, 0, 1), MB_CASE_UPPER);
        $betCategoryLabel = substr_replace($betCategoryLabel, $firstLetter, 0, 1);
        return $betCategoryLabel;
    }

    private function createDrawLabel(bool $teamType): string
    {
        if ($teamType === true) {
            $placeholder = "Nul";
        } else {
            $placeholder = "Aucun";
        }
        return $placeholder;
    }

    /** @param Object[] $choices by reference */
    private function createDrawChoice(array &$choices, bool $teamType): void
    {
        $choicesCount = count($choices);
        if ($teamType === true && $choicesCount === 2) {
            $last = $choices[1];
            $choices[1] = (object)["id" => 0,
                "label" => $this->createDrawLabel($teamType),
                "className" => ''
            ];
            $choices[] = $last;
        } else {
            array_unshift(
                $choices,
                (object)["id" => 0,
                    "label" => $this->createDrawLabel($teamType),
                    "className" => ''
                ]
            );
        }
    }

    /**
     * @param Object[] $choices by reference
     * @param array<int,Team> $betTeams
     */
    public function createTeamChoices(array &$choices, array $betTeams): void
    {
        foreach ($betTeams as $team) {
            $choices[] = (object)["id" => $team->getId(),
                "label" => $this->createChoiceLabel($team),
                "className" => Team::class
            ];
        }
    }

    /**
     * @param Object[] $choices by reference
     * @param array<int,Team> $betTeams
     */
    private function createMemberChoices(array &$choices, array $betTeams): void
    {
        $betMembers = [];
        foreach ($betTeams as $team) {
            $memberCollection = $team->getMembers();
            $betMembers = array_merge($betMembers, $memberCollection->toArray());
        }
        foreach ($betMembers as $member) {
            $choices[] = (object)["id" => $member->getId(),
                "label" => $this->createChoiceLabel($member),
                "className" => Member::class
            ];
        }
    }

    /**
     * @param array<int,Team> $betTeams
     * @return Object[]
     */
    private function createChoices(array $betTeams, BetCategory $betCategoryEntity): array
    {
        $this->categoryId = $betCategoryEntity->getId() ?? 0;
        $choices = [];
        $teamType = false;
        $choicesType = $betCategoryEntity->getTarget() ?? '';
        $allowDraw = $betCategoryEntity->getAllowDraw() ?? false;
        if ($choicesType === BetCategory::TEAM_TYPE) {
            $teamType = true;
            $this->createTeamChoices($choices, $betTeams);
        }
        if ($choicesType === BetCategory::MEMBER_TYPE) {
            $this->createMemberChoices($choices, $betTeams);
        }
        if ($allowDraw === true) {
            $this->createDrawChoice($choices, $teamType);
        }
        return $choices;
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
