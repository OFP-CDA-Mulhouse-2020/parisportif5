<?php

namespace App\Controller\Admin;

use App\Entity\Bet;
use App\Form\Bet\AdminManyBetResultFormType;
use App\Repository\RunRepository;
use App\Service\OddsStorageDataConverter;
use App\Form\Bet\BettingRegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Model\BettingRegistrationFormModel;
use App\Form\Handler\BettingRegistrationFormHandler;
use App\Repository\BetRepository;
use App\Repository\CompetitionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class BetResultController extends AbstractController
{
    public function __construct()
    {
    }

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
        $runTitle = $run->getEvent() . ' : ' . $run->getName();
        // manage
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //dd($request, $form->getData());
            $adminBetResultFormModel = $form->getData();
            $betCategoryId = $adminBetResultFormModel->getCategoryId();
            $betsForThisRun = $betRepository->findBy(
                [
                    'run' => $run,
                    'betCategory' => $betCategoryId,
                    'competition' => $run->getCompetition()
                ],
                [
                    'user' => 'ASC',
                    'competition' => 'ASC',
                    'run' => 'ASC',
                    'amount' => 'DESC'
                ]
            );
        }
        return $this->render('admin/bet_result/run.html.twig', [
            'run_title' => $runTitle,
            'run' => $run,
            'data' => $betCategories,
            'form' => $form->createView()
        ]);
    }
}
