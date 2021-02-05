<?php

namespace App\Controller;

use App\Entity\Bet;
use App\Entity\User;
use App\Repository\TeamRepository;
use App\Repository\MemberRepository;
use App\Repository\BetCategoryRepository;
use App\Repository\CompetitionRepository;
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

class BettingCompetitionRegistrationController extends AbstractController
{
    /**
     * @Route("/{sportSlug}/{competitionSlug}-{competitionId}/{betCategorySlug}-{betCategoryId}", name="competition_betting")
     */
    public function betIndex(
        Request $request,
        int $betCategoryId,
        int $competitionId,
        CompetitionRepository $competitionRepository,
        BetCategoryRepository $betCategoryRepository,
        DateTimeStorageDataConverter $dateTimeConverter,
        OddsStorageDataConverter $oddsStorageDataConverter,
        TeamRepository $teamRepository,
        MemberRepository $memberRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User $user */
        $user = $this->getUser();
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
        $bettingRegistrationFormModel = new BettingRegistrationFormModel($oddsStorageDataConverter);
        $bettingRegistrationFormModel->initializeWithCompetition($betCategory, $competition);
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
            'run' => '',
            'form' => $form->createView()
        ]);
    }
}
