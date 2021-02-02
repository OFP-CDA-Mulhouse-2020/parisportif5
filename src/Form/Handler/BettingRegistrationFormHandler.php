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
use App\Form\Model\BettingRegistrationFormModel;

final class BettingRegistrationFormHandler
{
    private BettingRegistrationFormModel $bettingRegistrationFormModel;

    public function __construct(BettingRegistrationFormModel $bettingRegistrationFormModel)
    {
        $this->bettingRegistrationFormModel = $bettingRegistrationFormModel;
    }

    public function handleForm(
        ObjectManager $entityManager,
        DateTimeStorageInterface $dateTimeConverter,
        OddsStorageInterface $oddsStorageDataConverter,
        BetCategory $betCategory,
        Run $run,
        Competition $competition,
        User $user
    ): void {
        $designation = $this->bettingRegistrationFormModel->getCategoryLabel();
        $amount = $this->bettingRegistrationFormModel->getAmount() ?? 0;
        $wallet = $user->getWallet();
        $walletAmmount = $wallet->getAmount() ?? 0;
        $newWalletAmount = (int)($walletAmmount - $amount);
        $choice = $this->bettingRegistrationFormModel->getResult();
        $bet = new Bet($dateTimeConverter);
        if ($choice instanceof Team) {
            $bet->setTeam($choice);
        }
        if ($choice instanceof Member) {
            $bet->setTeamMember($choice);
            $bet->setTeam($choice->getTeam());
        }
        $bet
            ->setCompetition($competition)
            ->setRun($run)
            ->setBetCategory($betCategory);
        $wallet->setAmount($newWalletAmount);
        $teamName = ($bet->getTeam() !== null) ? $bet->getTeam()->getName() : 'Nul';
        $designation .= ' ' . $teamName;
        $user->addOnGoingBet($bet);
        $date = new \DateTimeImmutable("now", new \DateTimeZone(DateTimeStorageDataConverter::STORED_TIME_ZONE));
        $bet
            ->setOdds($oddsStorageDataConverter->convertOddsMultiplierToStoredData(2))
            ->setUser($user)
            ->setDesignation($designation)
            ->setBetDate($date);
        $entityManager->persist($bet);
        $entityManager->flush();
    }
}
