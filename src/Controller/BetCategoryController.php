<?php

namespace App\Controller;

use App\Repository\CompetitionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BetCategoryController extends AbstractController
{
    /** @Route ("/{sportSlug}/{competitonSlug}/{competition_id}",
     *      name="betCategories")
     */
    public function index(
        CompetitionRepository $competitionRepository,
        int $competition_id
    ): Response {
        $competition = $competitionRepository
            ->find($competition_id);
        $sport = $competition->getSport();
        $betCategories = $competition->getBetCategories();
        return $this->render(
            'bet_category/betCategories.html.twig',
            [
                'site_title' => 'Paris Sportif',
                'page_title' => 'liste des paris disponibles',
                'sport' => $sport,
                'runs' => $competition->getRuns(),
                'betCategories' => $betCategories,
            ]
        );
    }
}
