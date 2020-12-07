<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Exception\InvalidCredentialsException;
use App\Form\UserLoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConnexionFormController extends AbstractController //dossier
{
    /**
     * @Route("/account/connect", name="Connexion")
     */
    public function renderDummyForm(Request $request): Response
    {
        $user = new User();
        $user->setEmailAddress('test123@mail.com');

        $form = $this->createForm(UserLoginType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('userloggedin');
        }
        // else {
        //     throw new InvalidCredentialsException("Les identifiants sont incorrects");
        // }

        return $this->render('dummy_form/index.html.twig', [
            'page_title' => 'Connexion',
            'form' => $form->createView()
        ]);
        //dump($request);
        //die();
    }

    /**
     * @Route("/account/logged", name="userloggedin")
     */

    public function redirectsToLoggedIn(): Response
    {
        return $this->render('dummy_form/userloggedin.html.twig', [
            'page_title' => 'User logged in',
        ]);
    }
}
