<?php

namespace App\Controller;

use App\DataConverter\DateTimeStorageDataConverter;
use App\DataConverter\OddsStorageDataConverter;
use App\Entity\BetCategory;
use App\Entity\Billing;
use App\Entity\Member;
use App\Entity\Team;
use App\Entity\User;
use App\Form\Bet\BetAdminFormType;
use App\Repository\BetCategoryRepository;
use App\Repository\BetRepository;
use App\Repository\RunRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BetAdminController extends AbstractController
{
    /** @const int LIMITATION_TO_SWITCH_TO_SELECT */
    public const LIMITATION_TO_SWITCH_TO_SELECT = 3;

    /**
     * @Route("/{sportSlug}/{competitonSlug}/{eventSlug}/{runSlug}-{runId}/{betCategorySlug}-{betCategoryId}/admin", name="bet_admin")
     */
    public function index(Request $request, int $runId, int $betCategoryId, RunRepository $runRepository, BetCategoryRepository $betCategoryRepository, BetRepository $betRepository): Response
    {
        //======================================>>>>>>>>>>>>>>>>>>>>>> donner resultat à run et competition
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User $user */
        $user = $this->getUser();
        //dd($request);
        if ($user->isAdmin() === false) {
            //return new RedirectResponse($request->server->get('HTTP_REFERER'));
            $allowUrl = "/" . $request->attributes->get('sportSlug') . "/" . $request->attributes->get('competitonSlug')
                . "/" . $request->attributes->get('eventSlug') . "/" . $request->attributes->get('runSlug') . '-' . $runId
                . "/" .  $request->attributes->get('betCategorySlug') . '-' . $betCategoryId;
            return new RedirectResponse($allowUrl);
        }
        $run = $runRepository->find($runId);
        if ($run === null) {
            return $this->redirectToRoute('userlogin');
        }
        $competition = $run->getCompetition();
        if ($competition === null) {
            return $this->redirectToRoute('userlogin');
        }
        $betCategory = $betCategoryRepository->find($betCategoryId);
        if ($betCategory === null) {
            return $this->redirectToRoute('userlogin');
        }
        $targetType = $betCategory->getTarget();
        $betCategoryLabel = 'Paris ' . mb_strtolower($betCategory->getName() ?? '');
        /*$firstLetter = mb_convert_case(mb_substr($betCategoryLabel, 0, 1), MB_CASE_UPPER);
        $betCategoryLabel = substr_replace($betCategoryLabel, $firstLetter, 0, 1);*/
        $runTargets = new ArrayCollection();
        $targetClassName = Team::class;
        if ($targetType === BetCategory::TEAM_TYPE) {
            $runTargets = $run->getTeams();
            $targetClassName = Team::class;
        }
        if ($targetType === BetCategory::MEMBER_TYPE) {
            $targetClassName = Member::class;
            $runTeams = $run->getTeams();
            $targetsArray = [];
            foreach ($runTeams as $team) {
                $memberCollection = $team->getMembers();
                $targetsArray = array_merge($targetsArray, $memberCollection->toArray());
            }
            $runTargets = new ArrayCollection($targetsArray);
        }
        $targetExpanded = true;
        /*if ($targetsCount > self::LIMITATION_TO_SWITCH_TO_SELECT) {
            $targetExpanded = false;
        }*/
        $targetRequired = true;
        $targetPlaceholder = "";
        if (!empty($betCategory->getAllowDraw())) {
            $targetRequired = false;
            $targetPlaceholder = "Nul";
        }
        $form = $this->createForm(BetAdminFormType::class, null, [
            'run_targets' => $runTargets,
            'target_placeholder' => $targetPlaceholder,
            'target_expanded' => $targetExpanded,
            'target_required' => $targetRequired,
            'category_label' => $betCategoryLabel,
            'class_name' => $targetClassName
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $oddsStorageDataConverter = new OddsStorageDataConverter();
            //$data = $form->getData();
            $data = $request->request->get("bet_admin_form");
            $winnerValue = !empty($data['winner']) ? (int)$data['winner'] : null;
            $bets = $betRepository->findBy(
                [
                    'run' => $run,
                    'betCategory' => $betCategory,
                    'competition' => $competition
                ],
                [
                    'user' => 'ASC',
                    'competition' => 'ASC',
                    'run' => 'ASC',
                    'amount' => 'DESC'
                ]
            );
            //dd($data, $request->request->all(), $winnerValue);
            foreach ($bets as $bet) {
                $team = $bet->getTeam() ?? null;
                $teamValue = ($team !== null) ? $team->getId() : null;
                $dateTimeConverter = new DateTimeStorageDataConverter();
                $billing = new Billing($dateTimeConverter);
                if ($teamValue === $winnerValue) {
                    $bet->won();
                    $betUser = $bet->getUser();
                    if (!is_null($betUser)) {
                        $betUserWallet = $betUser->getWallet();
                        if (!is_null($betUserWallet)) {
                            $amountStore = $bet->getAmount() ?? 0;
                            $oddsStore = $bet->getOdds() ?? 0;
                            $odds = $oddsStorageDataConverter->convertToOddsMultiplier($oddsStore);
                            $gains = $amountStore * $odds;
                            $profits = intval(round($gains * ((100 - Billing::DEFAULT_COMMISSION_RATE) * 0.01), 0, PHP_ROUND_HALF_UP));
                            $walletAmmount = $betUserWallet->getAmount() ?? 0;
                            $newWalletAmount = intval(($walletAmmount + $profits));
                            $betUserWallet->setAmount($newWalletAmount);
                            //$billing = new Billing();
                            //$dateTimeConverter = new DateTimeStorageDataConverter();
                            $commissionRateStore = $oddsStorageDataConverter->convertOddsMultiplierToStoredData(Billing::DEFAULT_COMMISSION_RATE);
                            $billing = $this->makeBill($billing, $user, $bet->getDesignation(), $profits, $commissionRateStore, $bet->getId(), Billing::CREDIT, $dateTimeConverter);
                            $entityManager->persist($billing);
                        }
                    }
                } else {
                    $bet->lost();
                    $betUser = $bet->getUser();
                    if (!is_null($betUser)) {
                        $amountStore = $bet->getAmount() ?? 0;
                        //$billing = new Billing();
                        //$dateTimeConverter = new DateTimeStorageDataConverter();
                        $billing = $this->makeBill($billing, $user, $bet->getDesignation(), $amountStore, 0, $bet->getId(), Billing::DEBIT, $dateTimeConverter);
                        $entityManager->persist($billing);
                    }
                }
            }
            // Add success message
            $this->addFlash(
                'success',
                "Les paris ont été mis à jour avec succès."
            );
            $entityManager->flush();
        }
        return $this->render('bet_admin/index.html.twig', [
            'run' => $run,
            'betCat' => $betCategory,
            'form' => $form->createView()
        ]);
    }

    protected function makeBill(Billing $billing, User $betUser, string $designation, int $amount, int $commissionRate, int $betId, string $operationType, DateTimeStorageDataConverter $dateTimeConverter): Billing
    {
        $date = new \DateTimeImmutable("now", new \DateTimeZone(DateTimeStorageDataConverter::STORED_TIME_ZONE));
        //\uniqid("$betId", true)
        $billing
            ->setDateTimeConverter($dateTimeConverter)
            ->setFirstName($betUser->getFirstName())
            ->setLastName($betUser->getLastName())
            ->setAddress($betUser->getBillingAddress())
            ->setCity($betUser->getBillingCity())
            ->setPostcode($betUser->getBillingPostcode())
            ->setCountry($betUser->getBillingCountry())
            ->setDesignation($designation)
            ->setAmount($amount)
            ->setCommissionRate($commissionRate)
            ->setUser($betUser)
            ->setOrderNumber($betId)
            ->setInvoiceNumber($betId)
            ->setIssueDate($date)
            ->setDeliveryDate($date)
            ->setOperationType($operationType);
        return $billing;
    }
}
