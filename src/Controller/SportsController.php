<?php

namespace App\Controller;

use App\Repository\CompetitionRepository;
use App\Repository\SportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SportsController extends AbstractController
{
    /**
     * @Route("/{sportSlug}/{id}", name="sport")
     */
    public function redirectsToSportPage(
        SportRepository $sportRepository,
        CompetitionRepository $competitionRepository
    ): Response {
        $sports = $sportRepository
            ->findAll();

        $competition = $competitionRepository
            ->findAll();
        return $this->render('sports/sport.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => "Liste des sports",
            'sports' => $sports,
            'competition' => $competition
        ]);
    }
}
