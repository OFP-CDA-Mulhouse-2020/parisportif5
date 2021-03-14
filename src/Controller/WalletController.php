<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BillingRepository;
use App\Form\Wallet\WalletAddFundsType;
use App\Form\Wallet\WalletRetireFundsType;
use App\Service\FundsStorageDataConverter;
use App\Form\Model\WalletAddFundsFormModel;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Model\WalletRetireFundsFormModel;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Handler\WalletAddFundsFormHandler;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Handler\WalletRetireFundsFormHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */
class WalletController extends AbstractController
{
    /**
     * @Route("/mon-compte/mes-finances/ajouter-des-fonds", name="account_wallet_add")
     */
    public function addFunds(Request $request): Response
    {
        // usually you'll want to make sure the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var User $user */
        $user = $this->getUser();

        $wallet = $user->getWallet();
        $walletAmount = $wallet->getAmount();

        $walletAddFundsFormModel = new WalletAddFundsFormModel(
            $wallet,
            $walletAmount
        );

        $form = $this->createForm(WalletAddFundsType::class, $walletAddFundsFormModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $walletAddFundsFormHandler = new WalletAddFundsFormHandler($walletAddFundsFormModel);
            $entityManager = $this->getDoctrine()->getManager();
            $walletAddFundsFormHandler->handleForm(
                $user,
                $wallet,
                $entityManager
            );

             // Add success message
             $this->addFlash(
                 'success',
                 "Le montant a été crédité avec succès à votre porte-monnaie."
             );
        }

        return $this->render('wallet/addfunds.html.twig', [
            'page_title' => 'Ajouter des fonds',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/mes-finances/retirer-des-fonds", name="account_wallet_retire")
     */
    public function retireFunds(Request $request): Response
    {
        // usually you'll want to make sure the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var User $user */
        $user = $this->getUser();

        $wallet = $user->getWallet();
        $walletAmount = $wallet->getAmount();

        $walletRetireFundsFormModel = new WalletRetireFundsFormModel(
            $wallet,
            $walletAmount
        );

        $form = $this->createForm(WalletRetireFundsType::class, $walletRetireFundsFormModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $walletRetireFundsFormHandler = new WalletRetireFundsFormHandler($walletRetireFundsFormModel);
            $entityManager = $this->getDoctrine()->getManager();
            $walletRetireFundsFormHandler->handleForm(
                $user,
                $wallet,
                $entityManager
            );

             // Add success message
             $this->addFlash(
                 'success',
                 "Le montant a été débité avec succès de votre porte-monnaie."
             );
        }

        return $this->render('wallet/retirefunds.html.twig', [
            'page_title' => 'Retirer des fonds',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-compte/mes-finances/historique-des-transactions", name="account_wallet_history")
     */
    public function listWalletTransactions(
        Request $request,
        BillingRepository $billingRepository,
        FundsStorageDataConverter $fundsStorageDataConverter
    ): Response {
        // usually you'll want to make sure the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var User $user */
        $user = $this->getUser();

        $currentDate = new \DateTimeImmutable("now", new \DateTimeZone('Europe/Paris'));

        $billingsList = $billingRepository->findBy(
            [
                'user' => $user
            ],
            [
                'issueDate' => 'DESC'
            ]
        ) ?? [];

        $walletHistory = [];
        foreach ($billingsList as $billing) {
            $listItem = $billing->toListItem();
            $listItem['amount'] = $fundsStorageDataConverter->convertToCurrencyUnit($listItem['amount']);
            $walletHistory[] = $listItem;
        }

        return $this->render('wallet/history.html.twig', [
            'page_title' => "Historique des transactions",
            'current_date' => $currentDate,
            'wallet_history' => $walletHistory,
            'currency_code' => User::SELECT_CURRENCY_CODE
        ]);
    }

    /**
     * @Route("/mon-compte/mes-finances", name="account_wallet_index")
     */
    public function viewWallet(Request $request, FundsStorageDataConverter $fundsStorageDataConverter): Response
    {
        // usually you'll want to make sure the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var User $user */
        $user = $this->getUser();

        $wallet = $user->getWallet();
        $walletAmount = $wallet->getAmount();
        $currentDate = new \DateTimeImmutable("now", new \DateTimeZone('Europe/Paris'));

        $walletDatas = [
            'amount' => $fundsStorageDataConverter->convertToCurrencyUnit($walletAmount),
            'currency' => User::SELECT_CURRENCY_CODE,
            'date' => $currentDate
        ];

        return $this->render('wallet/index.html.twig', [
            'page_title' => "Mes finances",
            'wallet_datas' => $walletDatas
        ]);
    }
}
