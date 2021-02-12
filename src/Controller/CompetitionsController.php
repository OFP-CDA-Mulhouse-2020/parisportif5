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
        return $this->render('competitions/competitions.html.twig', [
            'controller_name' => 'CompetitionsController',
            'page_title' => 'Listes des compétitions',
            'competitions' => $competitions
        ]);
    }
}
