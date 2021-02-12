<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\Bet;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Member;
use App\Entity\Wallet;
use App\Entity\BetCategory;
use App\Repository\TeamRepository;
use App\Repository\MemberRepository;
use Doctrine\Persistence\ObjectManager;
use App\DataConverter\OddsStorageInterface;
use App\Entity\Billing;
use App\Form\Model\AdminBetResultFormModel;

final class AdminBetResultFormHandler
{
    private AdminBetResultFormModel $adminBetResultFormModel;

    public function __construct(AdminBetResultFormModel $adminBetResultFormModel)
    {
        $this->adminBetResultFormModel = $adminBetResultFormModel;
    }

    private function setBetResult(
        Bet $bet,
        TeamRepository $teamRepository,
        MemberRepository $memberRepository
    ): Bet {
        $result = $this->adminBetResultFormModel->getResult();
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

    //Match 1 vs2 (2021-03-01 09:00 UTC) : Paris sur Vainqueur avec AS Saint-Étienne
    //Compétition championnat de fr - Pool n°1 - Match 1 vs2 (2021-03-01 09:00 UTC) : Paris sur Vainqueur avec AS Saint-Étienne
    // pour facturation Designation
    private function getDesignation(Bet $bet): string
    {
        $designation = '';
        $competition = $bet->getCompetition();
        $startDate = $competition->getStartDate();
        $competitionName = $competition->getName();
        $designation .= (!empty($competitionName)) ? $competitionName : '' ;
        $run = $bet->getRun();
        if ($run !== null) {
            $startDate = $run->getStartDate();
            $eventName = $run->getEvent();
            $runName = $run->getName();
            $designation .= (!empty($eventName)) ? ' - ' . $eventName : '' ;
            $designation .= (!empty($runName)) ? ' - ' . $runName : '' ;
        }
        $designation .= (!empty($startDate)) ? ' (' .  $startDate->format('Y-m-d H:i T') . ')' : '';
        return $designation;
    }

    protected function makeBill(Billing $billing, User $betUser, string $designation, int $amount, string $commissionRate, int $betId, string $operationType): Billing
    {
        $date = new \DateTimeImmutable("now", new \DateTimeZone(Bet::STORED_TIME_ZONE));
        //\uniqid("$betId", true)
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

    /** @param iterable<Bet> $bets */
    public function handleForm(
        ObjectManager $entityManager,
        iterable $bets,
        OddsStorageInterface $oddsStorageDataConverter,
        TeamRepository $teamRepository,
        MemberRepository $memberRepository
    ): void {
        $winnerValue = 0;
        //$winnerValue = !empty($data['winner']) ? (int)$data['winner'] : null;
        foreach ($bets as $bet) {
            $valid = false;
            $team = $bet->getTeam();
            $teamValue = ($team !== null) ? $team->getId() : null;
            $valid = ($teamValue === $winnerValue);
            //
            $billing = new Billing();
            if ($valid === true) {
                $bet->won();
                $betUser = $bet->getUser();
                if (!is_null($betUser)) {
                    $betUserWallet = $betUser->getWallet();
                    if (!is_null($betUserWallet)) {
                        $amountStore = $bet->getAmount() ?? 0;
                        $oddsStore = $bet->getOdds() ?? 0;
                        $odds = $oddsStorageDataConverter->convertToOddsMultiplier($oddsStore);
                        $gains = $amountStore * $odds;
                        $profits = (int)(round($gains * ((100 - Billing::DEFAULT_COMMISSION_RATE) * 0.01), 0, PHP_ROUND_HALF_UP));
                        $walletAmmount = $betUserWallet->getAmount() ?? 0;
                        $newWalletAmount = (int)(($walletAmmount + $profits));
                        $betUserWallet->setAmount($newWalletAmount);
                        $commissionRateStore = $oddsStorageDataConverter->convertOddsMultiplierToStoredData(Billing::DEFAULT_COMMISSION_RATE);
                        $billing = $this->makeBill($billing, $bet->getUser(), $bet->getDesignation(), $profits, $commissionRateStore, $bet->getId(), Billing::CREDIT);
                        $entityManager->persist($billing);
                    }
                }
            } else {
                $bet->lost();
                $betUser = $bet->getUser();
                if (!is_null($betUser)) {
                    $amountStore = $bet->getAmount() ?? 0;
                    $billing = $this->makeBill($billing, $bet->getUser(), $bet->getDesignation(), $amountStore, '0', $bet->getId(), Billing::DEBIT);
                    $entityManager->persist($billing);
                }
            }
        }
    }
}
