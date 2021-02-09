<?php

namespace App\Controller;

use App\Repository\RunRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BetCategoriesMatchsController extends AbstractController
{
    /**
     * @Route("/{sportSlug}/{competitionSlug}/{eventSlug}/{runSlug}/{run_id}", name="bet_categories_matchs")
     */
    public function index(
        RunRepository $runRepository,
        int $run_id
    ): Response {
        $run = $runRepository
            ->find($run_id);
        return $this->render('bet_categories_matchs/betCategoriesMatchs.html.twig', [
            'controller_name' => 'bet_categories_matchs',
            'site_title' => 'Paris Sportif',
            'page_title' => 'Liste des catégories de matchs.',
            'cats' => $run->getCompetition()->getBetCategories(),
        ]);
    }
}
