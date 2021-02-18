<?php

namespace App\Controller;

use App\Form\Wallet\AddFundsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */
class WalletController extends AbstractController
{
    /**
     * @Route("/mon-compte/ajouter-des-fonds", name="account_addfunds")
     */
    public function renderAddFundsPage(Request $request): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $wallet = $user->getWallet();
        $amount = $wallet->getAmount();

        $form = $this->createForm(AddFundsType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();

                $wallet->setAmount($amount + $data['amount']);

                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($wallet);
                $entityManager->flush();

                return $this->redirectToRoute('account_wallet');
            }
        }

        return $this->render('wallet/addfunds.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Ajouter des fonds',
            'form' => $form->createView(),
            'wallet_amount' => $amount
            ]);
    }

    /**
     * @Route("/mon-compte/porte-monnaie", name="account_wallet")
     */
    public function renderWallet(): Response
    {
        return $this->render('wallet/index.html.twig', [
            'site_title' => 'Paris Sportif',
            'page_title' => 'Porte monnaie'
        ]);
    }
}
