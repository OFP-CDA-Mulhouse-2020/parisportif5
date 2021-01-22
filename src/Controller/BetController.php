<?php

namespace App\Controller;

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
        $bet
            ->setCompetition($competition)
            ->setDesignation($designation)
            ->setRun($run)
            ->setBetCategory($betCategory);
        $form = $this->createForm(BetFormType::class, $bet, [
            'run_teams' => $run->getTeams()
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
                $date = new \DateTimeImmutable("now", new \DateTimeZone("UTC"));
                $bet
                    ->setOdds($bet->convertOddsMultiplierToStoredData(2))
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
