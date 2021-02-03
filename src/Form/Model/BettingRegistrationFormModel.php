<?php

namespace App\Form\Model;

use App\Entity\Run;
use App\Entity\Team;
use App\Entity\Member;
use App\Entity\BetCategory;
use App\Entity\Competition;
use App\Validator\UniqueBet;
use App\DataConverter\OddsStorageInterface;
use App\Service\DateTimeStorageDataConverter;
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

    /**
     * @Assert\NotNull(
     *     message="La date de soumission ne peut pas être vide."
     * )
     * @UniqueBet()
     */
    private ?\DateTimeImmutable $submitDate = null;

    private OddsStorageInterface $oddsStorageDataConverter;

    public function __construct(OddsStorageInterface $oddsStorageDataConverter)
    {
        $this->oddsStorageDataConverter = $oddsStorageDataConverter;
    }

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
     * @param array<int,Team> $betTeams
     * @return int Sum of odds
     */
    public function createTeamChoices(array &$choices, array $betTeams): int
    {
        $totalOdds = 0;
        foreach ($betTeams as $team) {
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
     * @param array<int,Team> $betTeams
     * @return int Sum of odds
     */
    private function createMemberChoices(array &$choices, array $betTeams): int
    {
        $totalOdds = 0;
        $betMembers = [];
        foreach ($betTeams as $team) {
            $memberCollection = $team->getMembers();
            $betMembers = array_merge($betMembers, $memberCollection->toArray());
        }
        foreach ($betMembers as $member) {
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

    /**
     * @param array<int,Team> $betTeams
     * @return Object[]
     */
    private function createChoices(array $betTeams, BetCategory $betCategoryEntity): array
    {
        $choices = [];
        $totalOdds = 0;
        $teamType = false;
        $choicesType = $betCategoryEntity->getTarget() ?? '';
        $allowDraw = $betCategoryEntity->getAllowDraw() ?? false;
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
            new \DateTimeZone(DateTimeStorageDataConverter::STORED_TIME_ZONE)
        );
        $this->submitDate = $this->getDateByTenthOfSecond($date);
        return $this;
    }

    public function getSubmitDate(): ?\DateTimeImmutable
    {
        return $this->submitDate;
    }
}
