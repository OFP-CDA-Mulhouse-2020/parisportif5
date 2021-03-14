<?php

namespace App\Controller;

use App\Entity\Bet;
use App\Entity\User;
use App\Repository\RunRepository;
use App\Repository\TeamRepository;
use App\Repository\MemberRepository;
use App\Repository\BetCategoryRepository;
use App\Service\OddsStorageDataConverter;
use App\Form\Bet\BettingRegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Model\BettingRegistrationFormModel;
use App\Form\Handler\BettingRegistrationFormHandler;
use App\Form\Model\BetChoiceGenerator;
use App\Repository\CompetitionRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BettingController extends AbstractController
{
    /**
     * @Route("/{sportSlug}/{competitionSlug}/{eventSlug}/{runSlug}-{runId}/paris-{betCategorySlug}-{betCategoryId}", name="run_betting")
     */
    public function bettingWithRun(
        Request $request,
        int $betCategoryId,
        int $runId,
        RunRepository $runRepository,
        BetCategoryRepository $betCategoryRepository,
        OddsStorageDataConverter $oddsStorageDataConverter,
        TeamRepository $teamRepository,
        MemberRepository $memberRepository
    ): Response {

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
        if ($run->canBet() === false) {
            $competitionUrl = "/" . $request->attributes->get('sportSlug') . "/"
                . $request->attributes->get('competitionSlug') . "/" . ($competition->getId() ?? '');
            return new RedirectResponse($competitionUrl);
        }
        $betChoiceGenerator = new BetChoiceGenerator($betCategory, $competition, $run, $oddsStorageDataConverter);
        $bettingRegistrationFormModel = new BettingRegistrationFormModel(
            $betChoiceGenerator->getChoices(),
            $betChoiceGenerator->getCategoryLabel(),
            $betChoiceGenerator->getCategoryId()
        );
        $form = $this->createForm(BettingRegistrationFormType::class, $bettingRegistrationFormModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // verification
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            /** @var User $user */
            $user = $this->getUser();
            // process
            $bettingRegistrationFormModel = $form->getData();
            $betAmount = $bettingRegistrationFormModel->getAmount() ?? 0;
            $wallet = $user->getWallet();
            if ($wallet->isValidOperation($betAmount) === true) {
                $bettingRegistrationFormHandler = new BettingRegistrationFormHandler($bettingRegistrationFormModel);
                $entityManager = $this->getDoctrine()->getManager();
                $bet = new Bet();
                $bet
                    ->setCompetition($competition)
                    ->setRun($run)
                    ->setBetCategory($betCategory);
                $bettingRegistrationFormHandler->handleForm(
                    $entityManager,
                    $oddsStorageDataConverter,
                    $bet,
                    $user,
                    $teamRepository,
                    $memberRepository
                );
                // Add success message
                $this->addFlash(
                    'success',
                    "Votre paris a été pris et validé avec succès."
                );
            } else {
                // Add missing funds message
                $this->addFlash(
                    'warning',
                    "Vous manquez de fonds pour poser votre paris."
                );
            }
        }
        return $this->render('bet/betting.html.twig', [
            'betCategory' => $betCategory,
            'competition' => $competition,
            'run' => $run,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{sportSlug}/{competitionSlug}-{competitionId}/paris-{betCategorySlug}-{betCategoryId}", name="competition_betting")
     */
    public function bettingWithCompetition(
        Request $request,
        int $betCategoryId,
        int $competitionId,
        CompetitionRepository $competitionRepository,
        BetCategoryRepository $betCategoryRepository,
        OddsStorageDataConverter $oddsStorageDataConverter,
        TeamRepository $teamRepository,
        MemberRepository $memberRepository
    ): Response {
        $competition = $competitionRepository->find($competitionId);
        if ($competition === null) {
            return $this->redirectToRoute('userlogin');
        }
        $betCategory = $betCategoryRepository->find($betCategoryId);
        if ($betCategory === null) {
            return $this->redirectToRoute('userlogin');
        }
        if ($competition->canBet() === false) {
            $sportUrl = "/" . $request->attributes->get('sportSlug') . "-"
                . ($competition->getSport()->getId() ?? '');
            return new RedirectResponse($sportUrl);
        }
        $betChoiceGenerator = new BetChoiceGenerator($betCategory, $competition, null, $oddsStorageDataConverter);
        $bettingRegistrationFormModel = new BettingRegistrationFormModel(
            $betChoiceGenerator->getChoices(),
            $betChoiceGenerator->getCategoryLabel(),
            $betChoiceGenerator->getCategoryId()
        );
        $form = $this->createForm(BettingRegistrationFormType::class, $bettingRegistrationFormModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // verification
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            /** @var User $user */
            $user = $this->getUser();
            // process
            $bettingRegistrationFormModel = $form->getData();
            $betAmount = $bettingRegistrationFormModel->getAmount() ?? 0;
            $wallet = $user->getWallet();
            if ($wallet->isValidOperation($betAmount) === true) {
                $bettingRegistrationFormHandler = new BettingRegistrationFormHandler($bettingRegistrationFormModel);
                $entityManager = $this->getDoctrine()->getManager();
                $bet = new Bet();
                $bet
                    ->setCompetition($competition)
                    ->setRun(null)
                    ->setBetCategory($betCategory);
                $bettingRegistrationFormHandler->handleForm(
                    $entityManager,
                    $oddsStorageDataConverter,
                    $bet,
                    $user,
                    $teamRepository,
                    $memberRepository
                );
                // Add success message
                $this->addFlash(
                    'success',
                    "Votre paris a été pris et validé avec succès."
                );
            } else {
                // Add missing funds message
                $this->addFlash(
                    'warning',
                    "Vous manquez de fonds pour poser votre paris."
                );
            }
        }
        return $this->render('bet/betting.html.twig', [
            'betCategory' => $betCategory,
            'competition' => $competition,
            'form' => $form->createView()
        ]);
    }
}
