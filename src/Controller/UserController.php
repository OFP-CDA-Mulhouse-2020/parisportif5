<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserCreationType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/account/creation", name="user_creation")
     */
    public function creationForm(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserCreationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $user = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $testUser = $userRepository->findOneByEmail($user->getEmail());

                if (empty($testUser)) {
                    $entityManager->persist($user);

                    $this->addFlash(
                        'success',
                        'Succès'
                    );
                } else {
                    $this->addFlash(
                        'notice',
                        'Double'
                    );
                }
                $entityManager->flush();
            } else {
                $this->addFlash(
                    'warning',
                    'Erreur'
                );
            }
            return $this->redirectToRoute('main');
        }
        return $this->render('user/new.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Créer un compte',
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/main", name="main")
     */
    public function mainPage(): Response
    {
        //return new Response('ok');
        return $this->render('base.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Accueil'
        ]);
    }
}
