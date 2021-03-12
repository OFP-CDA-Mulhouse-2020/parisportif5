<?php

namespace App\Controller;

use App\Repository\CompetitionRepository;
use App\Repository\RunRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RunsCompetitionsController extends AbstractController
{

    /** @Route ("/{sportSlug}/competition-{competitionSlug}/{competition_id}", name="runsCompetitions")*/
    public function index(
        CompetitionRepository $competitionRepository,
        RunRepository $runRepository,
        int $competition_id
    ): Response {
        $competition = $competitionRepository
            ->find($competition_id);
        $runs = $runRepository
            ->findBy(['competition' => $competition_id]);
        $runsWithUrl = [];
        foreach ($runs as $run) {
            $runUrl = $this->generateUrl('bet_categories_matchs', [
                'sportSlug' => urlencode($run->getCompetition()->getSport()->getName()),
                'competitionSlug' => urlencode($run->getCompetition()->getName()),
                'eventSlug' => urlencode($run->getEvent()),
                'runSlug' => urlencode($run->getName()),
                'run_id' => $run->getId()
            ]);
            $runsWithUrl[] = ['url' => $runUrl, 'entity' => $run];
        }
        return $this->render(
            'runs_competitions/runsCompetitions.html.twig',
            [
                'site_title' => 'Paris Sportif',
                'page_title' => "Liste des Runs",
                'RunsCompetition' => $runsWithUrl
            ]
        );
    }
}
