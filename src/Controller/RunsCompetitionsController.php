<?php

namespace App\Controller;

use App\Repository\CompetitionRepository;
use App\Repository\SportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RunsCompetitionsController extends AbstractController
{

    /** @Route ("/{sportSlug}/{sport_id}/{competitionSlug}/{competition_id}", name="runsCompetitions")*/
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
            'runs_competitions/runsCompetitions.html.twig',
            [
                'site_title' => 'Paris Sportif',
                'page_title' => "Liste des Runs",
                'RunsCompetition' => $competition->getRuns(),
                'sport' => $sport
            ]
        );
    }
}
