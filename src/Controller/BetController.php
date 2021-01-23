<?php

namespace App\Controller;

use App\DataConverter\DateTimeStorageDataConverter;
use App\DataConverter\OddsStorageDataConverter;
use App\Entity\User;
use App\Entity\Bet;
use App\Form\Bet\BetFormType;
use App\Repository\BetCategoryRepository;
use App\Repository\RunRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BetController extends AbstractController
{
    /** @const int LIMITATION_TO_SWITCH_TO_SELECT */
    public const LIMITATION_TO_SWITCH_TO_SELECT = 3;

    /**
     * @Route("/{sportSlug}/{competitonSlug}/{eventSlug}/{runSlug}-{runId}/{betCategorySlug}-{betCategoryId}", name="bet_index")
     */
    public function betIndex(Request $request, int $runId, int $betCategoryId, RunRepository $runRepository, BetCategoryRepository $betCategoryRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User $user */
        $user = $this->getUser();
        $run = $runRepository->find($runId);
        if ($run === null) {
            return $this->redirectToRoute('userlogin');
        }
        $competition = $run->getCompetition();
        if ($competition === null) {
            return $this->redirectToRoute('userlogin');
        }
        $betCategory = $betCategoryRepository->find($betCategoryId);
        if ($betCategory === null) {
            return $this->redirectToRoute('userlogin');
        }
        $bet = new Bet();
        $designation = $betCategory->getName();
        $oddsStorageDataConverter = new OddsStorageDataConverter();
        $bet
            ->setCompetition($competition)
            ->setDesignation($designation)
            ->setRun($run)
            ->setBetCategory($betCategory);
        $runTeams = $run->getTeams();
        $teamsCount = count($runTeams);
        $teamExpanded = true;
        if ($teamsCount > self::LIMITATION_TO_SWITCH_TO_SELECT) {
            $teamExpanded = false;
        }
        $teamRequired = true;
        $teamPlaceholder = "";
        if (!empty($betCategory->getAllowDraw())) {
            $teamRequired = false;
            $teamPlaceholder = "Nul - ";
            $totalOdds = 0;
            foreach ($runTeams as $team) {
                $odds = $team->getOdds() ?? 0;
                $totalOdds += $odds;
            }
            $averageOdds = intval(round(($totalOdds / $teamsCount), 0, PHP_ROUND_HALF_UP));
            $teamPlaceholder .= $oddsStorageDataConverter->convertToOddsMultiplier($averageOdds);
        }
        $form = $this->createForm(BetFormType::class, $bet, [
            'run_teams' => $runTeams,
            'converter' => $oddsStorageDataConverter,
            'team_placeholder' => $teamPlaceholder,
            'team_expanded' => $teamExpanded,
            'team_required' => $teamRequired
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bet = $form->getData();
            $amount = $bet->getAmount();
            $wallet = $user->getWallet();
            $walletAmmount = $wallet->getAmount() ?? 0;
            $newWalletAmount = $walletAmmount - $amount;
            //dd($bet);
            //dd($user);
            if ($newWalletAmount >= 0) {
                $wallet->setAmount($newWalletAmount);
                $teamName = ($bet->getTeam() !== null) ? $bet->getTeam()->getName() : 'Nul';
                $designation = $bet->getDesignation() . ' ' . $teamName;
                $user->addOnGoingBet($bet);
                $dateTimeConverter = new DateTimeStorageDataConverter();
                $date = new \DateTimeImmutable("now", new \DateTimeZone(DateTimeStorageDataConverter::STORED_TIME_ZONE));
                $bet
                    ->setDateTimeConverter($dateTimeConverter)
                    ->setOdds($oddsStorageDataConverter->convertOddsMultiplierToStoredData(2))
                    ->setUser($user)
                    ->setDesignation($designation)
                    ->setBetDate($date);
                //dd($bet);
                // Add success message
                $this->addFlash(
                    'success',
                    "Votre paris a été pris et validé avec succès."
                );
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($bet);
                $entityManager->flush();
            } else {
                // Add missing founds message
                $this->addFlash(
                    'warning',
                    "Vous manquez de fonds pour poser votre paris."
                );
            }
        }
        return $this->render('bet/index.html.twig', [
            'bet' => $bet,
            'form' => $form->createView()
        ]);
    }
}
