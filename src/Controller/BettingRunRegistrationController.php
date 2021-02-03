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
use App\Service\DateTimeStorageDataConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Model\BettingRegistrationFormModel;
use App\Form\Handler\BettingRegistrationFormHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BettingRunRegistrationController extends AbstractController
{
    /**
     * @Route("/{sportSlug}/{competitionSlug}/{eventSlug}/{runSlug}-{runId}/{betCategorySlug}-{betCategoryId}", name="run_betting")
     */
    public function betIndex(
        Request $request,
        int $betCategoryId,
        int $runId,
        RunRepository $runRepository,
        BetCategoryRepository $betCategoryRepository,
        DateTimeStorageDataConverter $dateTimeConverter,
        OddsStorageDataConverter $oddsStorageDataConverter,
        TeamRepository $teamRepository,
        MemberRepository $memberRepository
    ): Response {
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
        if ($run->isOngoing() || $run->isFinish()) {
            $competitionUrl = "/" . $request->attributes->get('sportSlug') . "/"
                . $request->attributes->get('competitionSlug') . "/" . ($competition->getId() ?? '');
            return new RedirectResponse($competitionUrl);
        }
        $bettingRegistrationFormModel = new BettingRegistrationFormModel($oddsStorageDataConverter);
        $bettingRegistrationFormModel->initializeWithRun($betCategory, $run);
        $form = $this->createForm(BettingRegistrationFormType::class, $bettingRegistrationFormModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bettingRegistrationFormModel = $form->getData();
            $betAmount = $bettingRegistrationFormModel->getAmount() ?? 0;
            $wallet = $user->getWallet();
            if ($wallet->isValidOperation($betAmount) === true) {
                $bettingRegistrationFormHandler = new BettingRegistrationFormHandler($bettingRegistrationFormModel);
                $entityManager = $this->getDoctrine()->getManager();
                $bet = new Bet($dateTimeConverter);
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
                // Add missing founds message
                $this->addFlash(
                    'warning',
                    "Vous manquez de fonds pour poser votre paris."
                );
            }
        }
        return $this->render('bet/index.html.twig', [
            'betCategory' => $betCategory,
            'competition' => $competition,
            'run' => $run,
            'form' => $form->createView()
        ]);
    }
}
