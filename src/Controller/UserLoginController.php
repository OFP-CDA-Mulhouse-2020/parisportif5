<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserLoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserLoginController extends AbstractController //dossier
{
    /**
     * @Route("/login", name="Connexion")
     */
    public function renderDummyForm(Request $request): Response
    {
        $user = new User();
        $user->setEmail('test123@mail.com');

        $form = $this->createForm(UserLoginType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('userloggedin');
        } else if ($form->isSubmitted() && !($form->isValid())) {
            return $this->render('login_form/loginlink.html.twig', [
                'site_title' => 'Paris Sportif',
                'page_title' => 'Connexion',
                'form' => $form->createView()
                ]);
        }

        return $this->render('login_form/index.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Connexion',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/logged", name="userloggedin")
     */

    public function redirectsToLoggedIn(): Response
    {
        return $this->render('login_form/userloggedin.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'User logged in',
        ]);
    }
}
