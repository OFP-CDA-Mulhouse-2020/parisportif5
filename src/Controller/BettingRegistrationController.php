<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RunRepository;
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

class BettingRegistrationController extends AbstractController
{
    /**
     * @Route("/{sportSlug}/{competitonSlug}/{eventSlug}/{runSlug}-{runId}/{betCategorySlug}-{betCategoryId}", name="betting")
     */
    public function betIndex(
        Request $request,
        int $betCategoryId,
        int $runId,
        RunRepository $runRepository,
        BetCategoryRepository $betCategoryRepository,
        DateTimeStorageDataConverter $dateTimeConverter,
        OddsStorageDataConverter $oddsStorageDataConverter
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
            $CompetitionUrl = "/" . $request->attributes->get('sportSlug') . "/"
                . $request->attributes->get('competitonSlug') . "/" . ($competition->getId() ?? '');
            return new RedirectResponse($CompetitionUrl);
        }
        $bettingRegistrationFormModel = new BettingRegistrationFormModel($oddsStorageDataConverter);
        $bettingRegistrationFormModel->initializeObject($betCategory, $run);
        $form = $this->createForm(BettingRegistrationFormType::class, $bettingRegistrationFormModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bettingRegistrationFormModel = $form->getData();
            $betAmount = $bettingRegistrationFormModel->getAmount() ?? 0;
            $wallet = $user->getWallet();
            if ($wallet->isValidSubtraction($betAmount) === true) {
                $entityManager = $this->getDoctrine()->getManager();
                $bettingRegistrationFormHandler = new BettingRegistrationFormHandler($bettingRegistrationFormModel);
                $bettingRegistrationFormHandler->handleForm(
                    $entityManager,
                    $dateTimeConverter,
                    $oddsStorageDataConverter,
                    $betCategory,
                    $run,
                    $competition,
                    $user
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
