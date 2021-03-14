<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BetSavedRepository;
use App\Service\FundsStorageDataConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */
class BetsListController extends AbstractController
{
    /**
     * @Route("/mon-compte/mes-paris", name="account_bets_index")
     */
    public function listBets(
        Request $request,
        BetSavedRepository $betSavedRepository,
        FundsStorageDataConverter $fundsStorageDataConverter
    ): Response {
        // usually you'll want to make sure the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var User $user */
        $user = $this->getUser();

        $betsList = $user->getOnGoingBets() ?? [];
        if (empty($betsList) === true) {
            $betsList = [];
        }

        $onGoingBets = [];
        foreach ($betsList as $bet) {
            $listItem = $bet->toListItem();
            $listItem['amount'] = $fundsStorageDataConverter->convertToCurrencyUnit($listItem['amount']);
            $onGoingBets[] = $listItem;
        }

        $betsSavedList = $betSavedRepository->findBy(
            [
                'user' => $user
            ],
            [
                'betDate' => 'DESC'
            ]
        ) ?? [];

        $betsHistory = [];
        foreach ($betsSavedList as $betSaved) {
            $listItem = $betSaved->toListItem();
            $listItem['amount'] = $fundsStorageDataConverter->convertToCurrencyUnit($listItem['amount']);
            $listItem['gains'] = $fundsStorageDataConverter->convertToCurrencyUnit($listItem['gains']);
            $listItem['status'] = $listItem['status'] ? 'Gagné' : 'Perdu';
            $betsHistory[] = $listItem;
        }

        return $this->render('bet/list.html.twig', [
            'page_title' => 'Mes paris',
            'tab_title_1' => 'Paris en cours',
            'tab_title_2' => 'Historique des paris',
            'bet_ongoing' => $onGoingBets,
            'bets_history' => $betsHistory,
            'currency_code' => User::SELECT_CURRENCY_CODE
        ]);
    }
}
