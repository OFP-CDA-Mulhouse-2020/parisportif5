<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\Bet;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Member;
use App\Entity\Billing;
use Doctrine\Persistence\ObjectManager;
use App\DataConverter\OddsStorageInterface;
use App\Entity\BetSaved;
use App\Form\Model\AdminBetResultFormModel;

final class AdminBetResultFormHandler
{
    private AdminBetResultFormModel $adminBetResultFormModel;

    public function __construct(AdminBetResultFormModel $adminBetResultFormModel)
    {
        $this->adminBetResultFormModel = $adminBetResultFormModel;
    }

    private function saveBet(ObjectManager $entityManager, Bet $bet, int $profits): void
    {
        $betUser = $bet->getUser();
        $betSaved = $this->createBetSaved($bet);
        $betSaved->setGains($profits);
        $betSaved->setUser($betUser);
        $entityManager->persist($betSaved);
        $betUser->removeOnGoingBet($bet);
    }

    private function createBetSaved(Bet $bet): BetSaved
    {
        $betSaved = new BetSaved();
        $betCategory = $bet->getBetCategory();
        $competition = $bet->getCompetition();
        $competitionSport = $competition->getSport();
        $betSaved
            ->setDesignation($bet->getDesignation() ?? '')
            ->setAmount($bet->getAmount() ?? 0)
            ->setBetDate($bet->getBetDate())
            ->setOdds($bet->getOdds() ?? 0)
            ->setIsWinning($bet->isWinning())
            ->setBetCategoryName($betCategory->getName() ?? '')
            ->setCompetitionName($competition->getName() ?? '')
            ->setCompetitionStartDate($competition->getStartDate())
            ->setCompetitionCountry($competition->getCountry() ?? '')
            ->setCompetitionSportName($competitionSport->getName() ?? '')
            ->setCompetitionSportCountry($competitionSport->getCountry() ?? '')
        ;
        $run = $bet->getRun();
        if (!is_null($run) === true) {
            $betSaved
                ->setRunEvent($run->getEvent() ?? '')
                ->setRunName($run->getName() ?? '')
                ->setRunStartDate($run->getStartDate())
            ;
        }
        $team = $bet->getTeam();
        if (!is_null($team) === true) {
            $betSaved
                ->setTeamName($team->getName() ?? '')
                ->setTeamCountry($team->getCountry() ?? '')
            ;
        }
        $member = $bet->getTeamMember();
        if (!is_null($member) === true) {
            $betSaved
                ->setMemberFirstName($member->getFirstName() ?? '')
                ->setMemberLastName($member->getLastName() ?? '')
                ->setMemberCountry($member->getCountry() ?? '')
            ;
        }
        return $betSaved;
    }

    // Compétition : championnat fr (2021-03-01) : Paris sur Vainqueur avec AS Saint-Étienne
    // Run : championnat fr - Pool n°1 - Match 1 vs2 (2021-03-01 09:00 UTC) : Paris sur Vainqueur avec AS Saint-Étienne
    private function getBillingDesignation(Bet $bet): string
    {
        $designation = '';
        $competition = $bet->getCompetition();
        $competitionStartDate = $competition->getStartDate();
        $startDate = $competitionStartDate->format('Y-m-d');
        $competitionName = $competition->getName();
        $designation .= (!empty($competitionName)) ? $competitionName : '' ;
        $run = $bet->getRun();
        if ($run !== null) {
            $runStartDate = $run->getStartDate();
            $startDate = $runStartDate->format('Y-m-d H:i T');
            $eventName = $run->getEvent();
            $runName = $run->getName();
            $designation .= (!empty($eventName)) ? ' - ' . $eventName : '' ;
            $designation .= (!empty($runName)) ? ' - ' . $runName : '' ;
        }
        $designation .= (trim($startDate) != '') ? ' (' .  $startDate . ')' : '';
        $designation .= (trim($designation) == '') ?: ' - ';
        $designation .= $bet->getDesignation();
        return $designation;
    }

    private function makeBill(ObjectManager $entityManager, Bet $bet, OddsStorageInterface $oddsStorageDataConverter): int
    {
        $bet->won();
        $betUser = $bet->getUser();
        $betUserWallet = $betUser->getWallet();
        $amountStore = $bet->getAmount() ?? 0;
        $oddsStore = $bet->getOdds() ?? 0;
        $odds = $oddsStorageDataConverter->convertToOddsMultiplier($oddsStore);
        $gains = $amountStore * $odds;
        $profits = (int)(round($gains * ((100 - Billing::DEFAULT_COMMISSION_RATE) * 0.01), 0, PHP_ROUND_HALF_UP));
        $betUserWallet->addAmount($profits);
        $commissionRateStore = $oddsStorageDataConverter->convertOddsMultiplierToStoredData(Billing::DEFAULT_COMMISSION_RATE);
        $billingDesignation = $this->getBillingDesignation($bet);
        $billing = $this->createBilling($betUser, $billingDesignation, $profits, $commissionRateStore, $bet->getId(), Billing::CREDIT);
        $entityManager->persist($billing);
        return $profits;
    }

    private function createBilling(User $betUser, string $designation, int $amount, string $commissionRate, int $betId, string $operationType): Billing
    {
        $date = new \DateTimeImmutable("now", new \DateTimeZone(Bet::STORED_TIME_ZONE));
        //\uniqid("$betId", true)
        $billing = new Billing();
        $billing
            ->setFirstName($betUser->getFirstName())
            ->setLastName($betUser->getLastName())
            ->setAddress($betUser->getBillingAddress())
            ->setCity($betUser->getBillingCity())
            ->setPostcode($betUser->getBillingPostcode())
            ->setCountry($betUser->getBillingCountry())
            ->setDesignation($designation)
            ->setAmount($amount)
            ->setCommissionRate($commissionRate)
            ->setUser($betUser)
            ->setOrderNumber($betId)
            ->setInvoiceNumber($betId)
            ->setIssueDate($date)
            ->setDeliveryDate($date)
            ->setOperationType($operationType)
        ;
        return $billing;
    }

    private function isValidBet(
        Bet $bet,
        ?int $resultId,
        string $resultClassName
    ): bool {
        $valid = false;
        if (is_null($resultId) === true) {
            $resultClassName = '';
            $team = $bet->getTeam();
            $member = $bet->getTeamMember();
            $valid = (is_null($member) && is_null($team));
        }
        if ($resultClassName === Team::class) {
            $team = $bet->getTeam();
            $teamId = ($team !== null) ? $team->getId() : null;
            $valid = ($teamId === $resultId);
        }
        if ($resultClassName === Member::class) {
            $member = $bet->getTeamMember();
            $memberId = ($member !== null) ? $member->getId() : null;
            $valid = ($memberId === $resultId);
        }
        return $valid;
    }

    /** @param iterable<Bet> $bets */
    public function handleForm(
        ObjectManager $entityManager,
        iterable $bets,
        OddsStorageInterface $oddsStorageDataConverter
    ): void {
        // + retirer bet from ongoing bet
        // + bet saved
        $result = $this->adminBetResultFormModel->getResult();
        $resultValue = $result->id;
        $resultId = empty($resultValue) ? null : (int)$resultValue;
        $resultClassName = $result->className;
        foreach ($bets as $bet) {
            $profits = 0;
            if ($this->isValidBet($bet, $resultId, $resultClassName) === true) {
                $profits = $this->makeBill($entityManager, $bet, $oddsStorageDataConverter);
            }
            $this->saveBet($entityManager, $bet, $profits);
        }
        $entityManager->flush();
    }
}
