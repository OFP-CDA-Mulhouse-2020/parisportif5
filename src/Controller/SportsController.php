<?php

namespace App\Controller;

use App\Repository\SportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SportsController extends AbstractController
{
    /**
     * @Route("/sports", name="sport")
     */
    public function redirectsToSportPage(
        SportRepository $sportRepository
    ): Response {
        $sports = $sportRepository
            ->findAll();
        $sportsWithUrl = [];
        foreach ($sports as $sport) {
            $sportUrl = $this->generateUrl('competitions', [
                'sportSlug' => urlencode($sport->getName()),
                'sport_id' => $sport->getId(),
            ]);
            $sportsWithUrl[] = ['url' => $sportUrl, 'entity' => $sport];
        }
        return $this->render('sports/sport.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => "Liste des sports",
            'sports' => $sportsWithUrl
        ]);
    }
}
