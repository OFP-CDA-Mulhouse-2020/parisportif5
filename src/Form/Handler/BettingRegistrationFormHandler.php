<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\Bet;
use App\Entity\Run;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Member;
use App\Entity\BetCategory;
use App\Entity\Competition;
use Doctrine\Persistence\ObjectManager;
use App\DataConverter\OddsStorageInterface;
use App\Service\DateTimeStorageDataConverter;
use App\DataConverter\DateTimeStorageInterface;
use App\Entity\Wallet;
use App\Form\Model\BettingRegistrationFormModel;
use App\Repository\MemberRepository;
use App\Repository\TeamRepository;

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
        $date = new \DateTimeImmutable("now", new \DateTimeZone(DateTimeStorageDataConverter::STORED_TIME_ZONE));
        $timeInArray = explode(':', $date->format('H:i:s'));
        $modulo = (int)$timeInArray[2] % 10;
        if ($modulo !== 0) {
            $timeInArray[2] = (int)$timeInArray[2] - $modulo;
        }
        $date = $date->setTime((int)$timeInArray[0], (int)$timeInArray[1], (int)$timeInArray[2]);
        $bet->setBetDate($date);
        return $bet;
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
        $designation = $this->bettingRegistrationFormModel->getCategoryLabel();
        $teamName = ($bet->getTeam() !== null) ? $bet->getTeam()->getName() : 'Nul';
        $designation .= ' ' . $teamName;
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
