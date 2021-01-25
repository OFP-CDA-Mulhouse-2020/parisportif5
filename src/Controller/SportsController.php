<?php

namespace App\Controller;

use App\Entity\Sport;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SportsController extends AbstractController
{
    /**
     * @Route("/{sportSlug}/{id}", name="sport")
     */
    public function redirectsToSportPage(int $id): Response
    {
        $sport = $this->getDoctrine()
            ->getRepository(Sport::class)
            ->find($id);

        return $this->render('sports/sport.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => "",
            'sport' => $sport
        ]);
    }
}
