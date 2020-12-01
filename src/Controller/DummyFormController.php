<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DummyFormController extends AbstractController
{
    /**
     * @Route("/account/create", name="formulaire_test")
     */
    public function renderDummyForm(): Response
    {
        return $this->render('dummy_form/index.html.twig', [
            'controller_name' => 'DummyFormController',
        ]);
    }
}
