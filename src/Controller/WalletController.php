<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wallet;
use App\Security\UserLoginAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Wallet\AddFundsType;
use App\Repository\WalletRepository;
use Symfony\Component\HttpFoundation\Request;

class WalletController extends AbstractController
{
    /**
     * @Route("/mon-compte/ajouter-des-fonds", name="account_addfunds")
     */
    public function renderAddFundsPage(Request $request, WalletRepository $walletRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); //test de redirection
        $user = $this->getUser();           //test d'accès à la donnée
        $wallet = $user->getWallet();
        //$wallet = $walletRepository->find($user->getWallet()->getId());           //test d'accès à la donnée
        $amount = $wallet->getAmount();           //test d'accès à la donnée


        $form = $this->createForm(AddFundsType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();
                // // var_dump($data['amount']);
                // // die();
                $wallet->setAmount($amount + $data['amount']);
                // var_dump($amount);
                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($wallet);
                $entityManager->flush();
                // var_dump($wallet);
                // die();
                return $this->redirectToRoute('fundsadded');
            }
        }

        return $this->render('wallet/index.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Ajouter des Fonds',
            'form' => $form->createView(),
            'wallet_amount' => $amount
            ]);
    }

    /**
     * @Route("/mon-compte/porte-monnaie", name="fundsadded")
     */
    public function redirectsToFundsAdded(): Response
    {
        return $this->render('wallet/fundsadded.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Fonds ajoutés'
        ]);
    }
}
