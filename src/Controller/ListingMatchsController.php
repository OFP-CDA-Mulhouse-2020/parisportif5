<?php

namespace App\Controller;

use App\Repository\CompetitionRepository;
use App\Repository\SportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingMatchsController extends AbstractController
{

    /** @Route ("/{sportSlug}/{sport_id}/{competitionSlug}/{competition_id}", name="listingMatch")*/
    public function index(
        SportRepository $sportRepository,
        int $sport_id,
        CompetitionRepository $competitionRepository,
        int $competition_id
    ): Response {
        $competition = $competitionRepository
            ->find($competition_id);
        $sport = $sportRepository
            ->find($sport_id);
        return $this->render(
            'run/listingMatchs.html.twig',
            [
                'site_title' => 'Paris Sportif',
                'page_title' => "Liste des Matchs",
                'RunsCompetition' => $competition->getRuns(),
                'sport' => $sport
            ]
        );
    }
}
