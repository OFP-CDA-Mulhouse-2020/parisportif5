<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\AddFundsType;
use Symfony\Component\HttpFoundation\Request;

class AddFundsController extends AbstractController
{
    /**
     * @Route("/account/addfunds", name="Ajouter des fonds")
     */
    public function renderAddFundsPage(Request $request): Response
    {
        $form = $this->createForm(AddFundsType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('fundsadded');
        }

        return $this->render('add_funds/index.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Ajouter des Fonds',
            'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/account/fundsadded", name="fundsadded")
     */
    public function redirectsToFundsAdded(): Response
    {
        return $this->render('add_funds/fundsadded.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Fonds ajoutés',
        ]);
    }
}
