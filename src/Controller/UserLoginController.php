<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserLoginType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserLoginController extends AbstractController //dossier
{
    // /**
    //  * @Route("/login", name="Connexion")
    //  */
    // public function renderDummyForm(Request $request, UserRepository $repo): Response
    // {
    //     $converter = new DateTimeStorageDataConverter();
    //     $user = new User($converter);
    //     $user->setEmail('test123@mail.com');

    //     $form = $this->createForm(UserLoginType::class, $user);

    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // var_dump($user);
    //         // die();
    //         $test = $repo->findOneBy(['email' => $user->getEmail()]);
    //        // if (!is_null($test)) {
    //             return $this->redirectToRoute('userloggedin');
    //         //}
    //     } else if ($form->isSubmitted() && !($form->isValid())) {
    //         return $this->render('login_form/loginlink.html.twig', [
    //             'page_title' => 'Connexion',
    //             'form' => $form->createView()
    //         ]);
    //     }

    //     return $this->render('login_form/index.html.twig', [
    //         'page_title' => 'Connexion',
    //         'form' => $form->createView()
    //     ]);
    // }
}
