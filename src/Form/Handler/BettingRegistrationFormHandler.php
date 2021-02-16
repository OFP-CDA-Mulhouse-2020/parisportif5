<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\Bet;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Member;
use App\Entity\Billing;
use App\Entity\BetCategory;
use App\Repository\TeamRepository;
use App\Repository\MemberRepository;
use Doctrine\Persistence\ObjectManager;
use App\DataConverter\OddsStorageInterface;
use App\Form\Model\BettingRegistrationFormModel;

final class BettingRegistrationFormHandler
{
    private BettingRegistrationFormModel $bettingRegistrationFormModel;

    public function __construct(BettingRegistrationFormModel $bettingRegistrationFormModel)
    {
        $this->bettingRegistrationFormModel = $bettingRegistrationFormModel;
    }

    private function setBetResult(
        Bet $bet,
        TeamRepository $teamRepository,
        MemberRepository $memberRepository
    ): Bet {
        $result = $this->bettingRegistrationFormModel->getResult();
        $className = $result->className;
        $choiceId =  $result->id;
        if ($choiceId === 0) {
            $className = '';
        }
        if ($className === Team::class) {
            $choice = $teamRepository->find($choiceId);
            $bet->setTeam($choice);
        }
        if ($className === Member::class) {
            $choice = $memberRepository->find($choiceId);
            $bet->setTeamMember($choice);
            $bet->setTeam($choice->getTeam());
        }
        return $bet;
    }

    private function setBetDate(Bet $bet): Bet
    {
        $date = $this->bettingRegistrationFormModel->getSubmitDate() ??
            new \DateTimeImmutable("now", new \DateTimeZone(Bet::STORED_TIME_ZONE));
        $date = $this->bettingRegistrationFormModel->getDateByTenthOfSecond($date);
        $bet->setBetDate($date);
        return $bet;
    }

    private function getSelectName(Bet $bet): string
    {
        $selectName = '';
        $selectObject = $bet->getSelect();
        if ($selectObject instanceof Team) {
            $selectName = $selectObject->getName() ?? '';
        }
        if ($selectObject instanceof Member) {
            $selectName = $selectObject->getFullName() ?? '';
        }
        if ($selectName === '') {
            $betTarget = $bet->getBetCategory()->getTarget();
            if ($betTarget === BetCategory::TEAM_TYPE) {
                $selectName = 'Nul';
            }
            if ($betTarget === BetCategory::MEMBER_TYPE) {
                $selectName = 'Auncun';
            }
        }
        return $selectName;
    }

    private function getDesignation(Bet $bet): string
    {
        $designation = 'Paris sur la catégorie ' . $this->bettingRegistrationFormModel->getCategoryLabel();
        $selectName = $this->getSelectName($bet);
        $designation .= ' avec le résultat ' . $selectName;
        return $designation;
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

    private function makeBill(ObjectManager $entityManager, Bet $bet): void
    {
        $bet->lost();
        $betUser = $bet->getUser();
        $amountStore = $bet->getAmount() ?? 0;
        $billingDesignation = $this->getBillingDesignation($bet);
        $billing = $this->createBilling($betUser, $billingDesignation, $amountStore, '0', $bet->getId(), Billing::DEBIT);
        $entityManager->persist($billing);
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
            ->setOperationType($operationType);
        return $billing;
    }

    public function handleForm(
        ObjectManager $entityManager,
        OddsStorageInterface $oddsStorageDataConverter,
        Bet $bet,
        User $user,
        TeamRepository $teamRepository,
        MemberRepository $memberRepository
    ): void {
        $wallet = $user->getWallet();
        $amount = $this->bettingRegistrationFormModel->getAmount() ?? 0;
        $wallet->subtractAmount($amount);
        $bet = $this->setBetResult($bet, $teamRepository, $memberRepository);
        $user->addOnGoingBet($bet);
        $bet = $this->setBetDate($bet);
        $designation = $this->getDesignation($bet);
        $result = $this->bettingRegistrationFormModel->getResult();
        $betOdds = $result->odds;
        $bet
            ->setAmount($amount)
            ->setOdds($oddsStorageDataConverter->convertOddsMultiplierToStoredData($betOdds))
            ->setUser($user)
            ->setDesignation($designation);
        $this->makeBill($entityManager, $bet);
        $entityManager->persist($bet);
        $user->addOnGoingBet($bet);
        $entityManager->flush();
    }
}
