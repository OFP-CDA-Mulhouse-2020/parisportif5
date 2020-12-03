<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserLoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DummyFormController extends AbstractController //rename + dossier
{
    /**
     * @Route("/account/connect", name="Connexion")
     */
    public function renderDummyForm(): Response
    {
        $user = new User();
        $user->setEmailAddress('test@mail.com');
        $user->setPassword('Passtest');

        $form = $this->createForm(UserLoginType::class, $user);

        return $this->render('dummy_form/index.html.twig', [
            'page_title' => 'Connexion',
            'form' => $form->createView()
        ]);
    }
}
