<?php

namespace App\Form\Model;

use App\Entity\Run;
use App\Entity\Team;
use App\Entity\Member;
use App\Entity\BetCategory;
use App\Entity\Competition;
use App\DataConverter\OddsStorageInterface;

final class BetChoiceGenerator
{
    private ?OddsStorageInterface $oddsStorageDataConverter = null;

    private BetCategory $betCategoryEntity;

    private Competition $competitionEntity;

    private ?Run $runEntity = null;

    private bool $adminModel = false;

    public function __construct(
        BetCategory $betCategoryEntity,
        Competition $competitionEntity,
        ?Run $runEntity = null,
        ?OddsStorageInterface $oddsStorageDataConverter = null,
        bool $adminModel = false
    ) {
        $this->oddsStorageDataConverter = $oddsStorageDataConverter;
        $this->betCategoryEntity = $betCategoryEntity;
        $this->competitionEntity = $competitionEntity;
        $this->runEntity = $runEntity;
        $this->adminModel = $adminModel;
    }

    /** @return array<int,Team> */
    private function initializeWithRun(): array
    {
        return $this->runEntity->getTeams()->toArray();
    }

    /** @return array<int,Team> */
    private function initializeWithCompetition(): array
    {
        return $this->competitionEntity->getTeams();
    }

    public function getCategoryLabel(): string
    {
        return $this->createCategoryLabel($this->betCategoryEntity);
    }

    private function createChoiceLabel(object $choice, string $odds = ''): string
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
        if ($this->adminModel === false) {
            $label = $odds . ' - ' . $label;
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

    private function createDrawLabel(bool $teamType, string $drawOdds = ''): string
    {
        if ($teamType === true) {
            $placeholder = "Nul";
        } else {
            $placeholder = "Aucun";
        }
        if ($this->adminModel === false) {
            $placeholder = $drawOdds . ' - ' . $placeholder;
        }
        return $placeholder;
    }

    private function createChoiceObject(int $id, string $label, string $classNane, float $odds = 0): object
    {
        $choiceArray = [
            "id" => $id,
            "label" => $label,
            "className" => $classNane
        ];
        if ($this->adminModel === false) {
            $choiceArray['odds'] = $odds;
        }
        return (object)$choiceArray;
    }

    /** @param Object[] $choices by reference */
    private function createDrawChoice(array &$choices, bool $teamType, int $totalOdds = 0): void
    {
        $choicesCount = count($choices);
        $drawOdds = 0;
        $drawOddsLabel = '';
        if ($this->adminModel === false) {
            $drawOdds = (float)(round(($totalOdds / $choicesCount), 0, PHP_ROUND_HALF_UP));
            $drawOddsLabel = (string)$drawOdds;
        }
        if ($teamType === true && $choicesCount === 2) {
            $last = $choices[1];
            $choices[1] = $this->createChoiceObject(
                0,
                $this->createDrawLabel($teamType, $drawOddsLabel),
                '',
                (float)$drawOdds
            );
            $choices[] = $last;
        } else {
            array_unshift(
                $choices,
                $this->createChoiceObject(
                    0,
                    $this->createDrawLabel($teamType, $drawOddsLabel),
                    '',
                    (float)$drawOdds
                )
            );
        }
    }

    /**
     * @param Object[] $choices by reference
     * @param array<int,Team> $betTeams
     */
    private function createTeamChoices(array &$choices, array $betTeams): int
    {
        $totalOdds = 0;
        foreach ($betTeams as $team) {
            $odds = 0;
            $oddsLabel = '';
            if ($this->adminModel === false) {
                $odds = $team->getOdds() ?? 0;
                $odds = $this->oddsStorageDataConverter->convertToOddsMultiplier($odds);
                $oddsLabel = (string)$odds;
                $totalOdds += $odds;
            }
            $choices[] = $this->createChoiceObject(
                $team->getId(),
                $this->createChoiceLabel($team, $oddsLabel),
                Team::class,
                (float)$odds
            );
        }
        return $totalOdds;
    }

    /**
     * @param Object[] $choices by reference
     * @param array<int,Team> $betTeams
     */
    private function createMemberChoices(array &$choices, array $betTeams): int
    {
        $betMembers = [];
        foreach ($betTeams as $team) {
            $memberCollection = $team->getMembers();
            $betMembers = array_merge($betMembers, $memberCollection->toArray());
        }
        $totalOdds = 0;
        foreach ($betMembers as $member) {
            $odds = 0;
            $oddsLabel = '';
            if ($this->adminModel === false) {
                $odds = $member->getOdds() ?? 0;
                $odds = $this->oddsStorageDataConverter->convertToOddsMultiplier($odds);
                $oddsLabel = (string)$odds;
                $totalOdds += $odds;
            }
            $choices[] = $this->createChoiceObject(
                $member->getId(),
                $this->createChoiceLabel($member, $oddsLabel),
                Member::class,
                (float)$odds
            );
        }
        return $totalOdds;
    }

    /** @return array<int,Team> */
    private function getBetTeams(): array
    {
        $betTeams = [];
        if (is_null($this->runEntity) === false) {
            $betTeams = $this->initializeWithRun();
        } else {
            $betTeams = $this->initializeWithCompetition();
        }
        return $betTeams;
    }

    /** @return Object[] */
    public function getChoices(): array
    {
        $betTeams = $this->getBetTeams();
        $choices = [];
        $totalOdds = 0;
        $teamType = false;
        $choicesType = $this->betCategoryEntity->getTarget() ?? '';
        $allowDraw = $this->betCategoryEntity->getAllowDraw() ?? false;
        if ($choicesType === BetCategory::TEAM_TYPE) {
            $teamType = true;
            $totalOdds = $this->createTeamChoices($choices, $betTeams);
        }
        if ($choicesType === BetCategory::MEMBER_TYPE) {
            $totalOdds = $this->createMemberChoices($choices, $betTeams);
        }
        if ($allowDraw === true) {
            $this->createDrawChoice($choices, $teamType, $totalOdds);
        }
        return $choices;
    }

    public function getCategoryId(): int
    {
        return $this->betCategoryEntity->getId() ?? 0;
    }
}
