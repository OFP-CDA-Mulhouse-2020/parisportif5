<?php

namespace App\Controller;

use App\Repository\CompetitionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RunsCompetitionsController extends AbstractController
{

    /** @Route ("/{sportSlug}/{competitionSlug}/{competition_id}", name="runsCompetitions")*/
    public function index(
        CompetitionRepository $competitionRepository,
        int $sport_id
    ): Response {
        $competitions = $competitionRepository
            ->findBy(['sport' => $sport_id]);
        $runs = $competitions;
        return $this->render(
            'runs_competitions/runsCompetitions.html.twig',
            [
                'site_title' => 'Paris Sportif',
                'page_title' => "Liste des Runs",
                'RunsCompetition' => $runs
            ]
        );
    }
}
