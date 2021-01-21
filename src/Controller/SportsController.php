<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SportsController extends AbstractController
{



    /**
     * @Route("/account/sports/football", name="page-football")
     */

    public function redirectsToFootballPage(): Response
    {
        return $this->render('sports/football.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Football',
        ]);
    }

    /**
     * @Route("/account/sports/handball", name="page-handball")
     */

    public function redirectsToHandballPage(): Response
    {
        return $this->render('sports/handball.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Handball',
        ]);
    }

    /**
     * @Route("/account/sports/tennis", name="page-tennis")
     */

    public function redirectsToTennisPage(): Response
    {
        return $this->render('sports/tennis.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Tennis',
        ]);
    }

    /**
     * @Route("/account/sports/tennisdetable", name="page-tennis-de-table")
     */

    public function redirectsToTableTennisPage(): Response
    {
        return $this->render('sports/tennisdetable.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Tennis de table',
        ]);
    }

    /**
     * @Route("/account/sports/formulaone", name="page-formula-one")
     */

    public function redirectsToFormulaOnePage(): Response
    {
        return $this->render('sports/formulaone.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Formule 1',
        ]);
    }
}
