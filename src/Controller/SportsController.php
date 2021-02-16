<?php

namespace App\Controller;

use App\Entity\Sport;
use App\Entity\Team;
use App\Repository\SportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SportsController extends AbstractController
{
    /**
     * @Route("/sports", name="sport")
     */
    public function redirectsToSportPage(SportRepository $sportRepository): Response
    {
        $sport = $sportRepository->findAllAttachedToCompetition();
        dd($sport);
        return $this->render('sports/sport.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => "",
            'sport' => $sport
        ]);
    }
}
