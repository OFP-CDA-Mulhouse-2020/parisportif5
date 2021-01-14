<?php

namespace App\Controller;

use App\Form\SecurityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/userlogin", name="userlogin")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('userloggedin');//test redirection
        }

        $form = $this->createForm(SecurityType::class);

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Connexion',
            'last_username' => $lastUsername,
            'error' => $error,
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

    /**
     * @Route("/userlogout", name="userlogout")
     */
    public function logout(): Response
    {
        throw new \LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }
}
