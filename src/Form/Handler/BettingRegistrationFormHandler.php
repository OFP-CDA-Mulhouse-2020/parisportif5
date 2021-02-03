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
use App\Service\DateTimeStorageDataConverter;
use App\Form\Model\BettingRegistrationFormModel;

final class BettingRegistrationFormHandler
{
    private BettingRegistrationFormModel $bettingRegistrationFormModel;

    public function __construct(BettingRegistrationFormModel $bettingRegistrationFormModel)
    {
        $this->bettingRegistrationFormModel = $bettingRegistrationFormModel;
    }

    private function changeWalletAmount(Wallet $wallet, int $amount): Wallet
    {
        $walletAmmount = $wallet->getAmount();
        $newWalletAmount = (int)($walletAmmount - $amount);
        $wallet->setAmount($newWalletAmount);
        return $wallet;
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
            new \DateTimeImmutable("now", new \DateTimeZone(DateTimeStorageDataConverter::STORED_TIME_ZONE));
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
        $wallet = $this->changeWalletAmount($wallet, $amount);
        $bet = $this->setBetResult($bet, $teamRepository, $memberRepository);
        //$designation = $this->getDesignation($bet);
        //$designation .= ' : Paris sur ' . $this->bettingRegistrationFormModel->getCategoryLabel();
        $designation = 'Paris sur ' . $this->bettingRegistrationFormModel->getCategoryLabel();
        $selectName = $this->getSelectName($bet);
        $designation .= ' avec ' . $selectName;
        $user->addOnGoingBet($bet);
        $bet = $this->setBetDate($bet);
        $result = $this->bettingRegistrationFormModel->getResult();
        $betOdds = $result->odds;
        $bet
            ->setAmount($amount)
            ->setOdds($oddsStorageDataConverter->convertOddsMultiplierToStoredData($betOdds))
            ->setUser($user)
            ->setDesignation($designation);
        $entityManager->persist($bet);
        $entityManager->flush();
    }
}
