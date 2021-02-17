<?php

namespace App\Controller;

use App\Repository\CompetitionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompetitionsController extends AbstractController
{
    /**
     * @Route("/{sportSlug}/{sport_id}", name="competitions")
     */
    public function index(
        CompetitionRepository $competitionRepository,
        int $sport_id
    ): Response {
        $competitions = $competitionRepository
            ->findBy(['sport' => $sport_id]);
        $competitionsWithUrl = [];
        foreach ($competitions as $competition) {
            $competitionUrl = $this->generateUrl('runsCompetitions', [
                'sportSlug' => urlencode($competition->getSport()->getName()),
                'competitionSlug' => urlencode($competition->getName()),
                'competition_id' => $competition->getId()
            ]);
            $competitionsWithUrl[] = ['url' => $competitionUrl, 'entity' => $competition];
        }
        return $this->render('competitions/competitions.html.twig', [
            'controller_name' => 'CompetitionsController',
            'page_title' => 'Listes des compétitions',
            'competitions' => $competitionsWithUrl
        ]);
    }
}
