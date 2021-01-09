<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddFundsController extends AbstractController
{
    /**
     * @Route("/account/addfunds", name="Ajouter des fonds")
     */
    public function renderAddFundsPage(): Response
    {
        return $this->render('add_funds/index.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Ajouter des Fonds'
            ]);
    }
}
