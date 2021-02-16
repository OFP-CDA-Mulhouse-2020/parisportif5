<?php

namespace App\Controller\Admin;

use App\Repository\BetRepository;
use App\Repository\RunRepository;
use App\Repository\CompetitionRepository;
use App\Service\OddsStorageDataConverter;
use App\Form\Bet\AdminManyBetResultFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Handler\AdminBetResultFormHandler;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class BetResultController extends AbstractController
{
    /**
     * @Route("/admin/run/bet-result/{runId}", name="admin_run_bet_result")
     */
    public function bettingWithRun(
        Request $request,
        int $runId,
        RunRepository $runRepository,
        BetRepository $betRepository,
        OddsStorageDataConverter $oddsStorageDataConverter
    ): Response {
        $run = $runRepository->find($runId);
        if ($run === null) {
            return $this->redirectToRoute('admin');
        }
        $betCategories = $run->getBetCategories();
        $form = $this->createForm(AdminManyBetResultFormType::class, null, [
            'target' => $run,
            'data_list' => $betCategories,
            'required' => false
        ]);
        $competition = $run->getCompetition();
        $competitionTitle = $competition->getName();
        $runTitle = $competitionTitle . ' : ' . $run->getEvent() . ' : ' . $run->getName();
        // manage form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //dd($request, $form->getData());
            $adminBetResultFormModel = $form->getData();
            $betCategoryId = $adminBetResultFormModel->getCategoryId();
            $betsForThisRun = $betRepository->findBy(
                [
                    'run' => $run,
                    'betCategory' => $betCategoryId,
                    'competition' => $competition
                ],
                [
                    'user' => 'ASC',
                    'competition' => 'ASC',
                    'run' => 'ASC',
                    'amount' => 'DESC'
                ]
            );
            $entityManager = $this->getDoctrine()->getManager();
            $adminBetResultFormHandler = new AdminBetResultFormHandler($adminBetResultFormModel);
            $adminBetResultFormHandler->handleForm(
                $entityManager,
                $betsForThisRun,
                $oddsStorageDataConverter
            );
            // Add success message
            $this->addFlash(
                'success',
                "Les paris concerné ont été modifé avec succès."
            );
        }
        return $this->render('admin/bet_result/run.html.twig', [
            'run_title' => $runTitle,
            'run' => $run,
            'data' => $betCategories,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/competition/bet-result/{competitionId}", name="admin_competition_bet_result")
     */
    public function bettingWithCompetition(
        Request $request,
        int $competitionId,
        CompetitionRepository $competitionRepository,
        BetRepository $betRepository,
        OddsStorageDataConverter $oddsStorageDataConverter
    ): Response {
        $competition = $competitionRepository->find($competitionId);
        if ($competition === null) {
            return $this->redirectToRoute('admin');
        }
        $betCategories = $competition->getBetCategoriesForCompetition();
        $form = $this->createForm(AdminManyBetResultFormType::class, null, [
            'target' => $competition,
            'data_list' => $betCategories,
            'required' => false
        ]);
        $competitionTitle = $competition->getName() . ' : ' . $competition->getStartDate()->format('Y-m-d');
        // manage form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //dd($request, $form->getData());
            $adminBetResultFormModel = $form->getData();
            $betCategoryId = $adminBetResultFormModel->getCategoryId();
            $betsForThisCompetition = $betRepository->findBy(
                [
                    'run' => null,
                    'betCategory' => $betCategoryId,
                    'competition' => $competition
                ],
                [
                    'user' => 'ASC',
                    'competition' => 'ASC',
                    'amount' => 'DESC'
                ]
            );
            $entityManager = $this->getDoctrine()->getManager();
            $adminBetResultFormHandler = new AdminBetResultFormHandler($adminBetResultFormModel);
            $adminBetResultFormHandler->handleForm(
                $entityManager,
                $betsForThisCompetition,
                $oddsStorageDataConverter
            );
            // Add success message
            $this->addFlash(
                'success',
                "Les paris concerné ont été modifé avec succès."
            );
        }
        return $this->render('admin/bet_result/competition.html.twig', [
            'run_title' => $competitionTitle,
            'run' => $competition,
            'data' => $betCategories,
            'form' => $form->createView()
        ]);
    }
}
