<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\BetAdminFormType;
use App\Repository\BetCategoryRepository;
use App\Repository\BetRepository;
use App\Repository\RunRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BetAdminController extends AbstractController
{
    /**
     * @Route("/{sportSlug}/{competitonSlug}/{eventSlug}/{runSlug}-{runId}/{betCategorySlug}-{betCategoryId}/admin", name="bet_admin")
     */
    public function index(Request $request, int $runId, int $betCategoryId, RunRepository $runRepository, BetCategoryRepository $betCategoryRepository, BetRepository $betRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
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
        $form = $this->createForm(BetAdminFormType::class, null, [
            'run_teams' => $run->getTeams()
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $winnerId = (int) $data['winner'] ?? null;
            $bets = $betRepository->findBy(
                [
                    'user' => $user,
                    'run' => $run,
                    'betCategory' => $betCategory,
                    'competition' => $competition
                ],
                [
                    'amount' => 'DESC'
                ]
            );
            foreach ($bets as $bet) {
                $team = $bet->getTeam() ?? null;
                $teamId = ($team !== null) ? $team->getId() : null;
                if ($teamId === $winnerId) {
                    $bet->won();
                } else {
                    $bet->lost();
                }
            }
            // Add success message
            $this->addFlash(
                'success',
                "Les paris ont été mis à jour avec succès."
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }
        return $this->render('bet_admin/index.html.twig', [
            'controller_name' => 'BetAdminController',
        ]);
    }
}
