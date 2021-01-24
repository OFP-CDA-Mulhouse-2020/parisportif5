<?php

namespace App\Controller;

use App\Entity\Sport;
use App\Repository\SportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserLoggedInterfaceController extends AbstractController
{

    /**
     * @Route("/account/logged", name="userloggedin")
     */

    public function redirectsToLoggedIn(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); //test de redirection

        $sport = $this->getDoctrine()->getRepository(Sport::class);
        // $sportRepository = $this->getDoctrine()->getRepository(SportRepository::class);
        $sports = $sport->findAll();
        $sportsCount = count($sports);
        // $sportID = $sport->findOneBy(['id']);
        // foreach ($sports as $sport){
            //  var_dump(count($sports));
        // }
        // die();

        return $this->render('login_form/userloggedin.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'User logged in',
            'sports' => $sports
        ]);
    }
}
