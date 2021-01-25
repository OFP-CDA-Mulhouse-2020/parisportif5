<?php

namespace App\Controller;

use App\DataConverter\DateTimeStorageDataConverter;
use App\DataConverter\OddsStorageDataConverter;
use App\Entity\User;
use App\Entity\Bet;
use App\Entity\BetCategory;
use App\Entity\Member;
use App\Entity\Team;
use App\Form\Bet\BetFormType;
use App\Repository\BetCategoryRepository;
use App\Repository\RunRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BetController extends AbstractController
{
    /** @const int LIMITATION_TO_SWITCH_TO_SELECT */
    public const LIMITATION_TO_SWITCH_TO_SELECT = 3;

    /**
     * @Route("/{sportSlug}/{competitonSlug}/{eventSlug}/{runSlug}-{runId}/{betCategorySlug}-{betCategoryId}", name="bet_index")
     */
    public function betIndex(Request $request, int $runId, int $betCategoryId, RunRepository $runRepository, BetCategoryRepository $betCategoryRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User $user */
        $user = $this->getUser();
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
        $dateTimeConverter = new DateTimeStorageDataConverter();
        $bet = new Bet($dateTimeConverter);
        $betCategoryLabel = $betCategory->getName() ?? '';
        $firstLetter = mb_convert_case(mb_substr($betCategoryLabel, 0, 1), MB_CASE_UPPER);
        $betCategoryLabel = substr_replace($betCategoryLabel, $firstLetter, 0, 1);
        $designation = $betCategoryLabel;
        $oddsStorageDataConverter = new OddsStorageDataConverter();
        $bet
            ->setCompetition($competition)
            ->setDesignation($designation)
            ->setRun($run)
            ->setBetCategory($betCategory);
        $runTargets = new ArrayCollection();
        $targetClassName = Team::class;
        $propertyMapped = 'team';
        if ($targetType === BetCategory::TEAM_TYPE) {
            $runTargets = $run->getTeams();
            $propertyMapped = 'team';
            $targetClassName = Team::class;
        }
        if ($targetType === BetCategory::MEMBER_TYPE) {
            $targetClassName = Member::class;
            $propertyMapped = 'teamMember';
            $runTeams = $run->getTeams();
            $targetsArray = [];
            foreach ($runTeams as $team) {
                $memberCollection = $team->getMembers();
                $targetsArray = array_merge($targetsArray, $memberCollection->toArray());
            }
            $runTargets = new ArrayCollection($targetsArray);
        }
        $targetsCount = count($runTargets);
        $targetExpanded = true;
        /*if ($targetsCount > self::LIMITATION_TO_SWITCH_TO_SELECT) {
            $targetExpanded = false;
        }*/
        $targetRequired = true;
        $targetPlaceholder = "";
        if (!empty($betCategory->getAllowDraw())) {
            $targetRequired = false;
            $targetPlaceholder = "Nul";
            $totalOdds = 0;
            foreach ($runTargets as $target) {
                $odds = $target->getOdds() ?? 0;
                $totalOdds += $odds;
            }
            $averageOdds = intval(round(($totalOdds / $targetsCount), 0, PHP_ROUND_HALF_UP));
            $targetPlaceholder = $oddsStorageDataConverter->convertToOddsMultiplier($averageOdds) . ' - ' . $targetPlaceholder;
        }
        $form = $this->createForm(BetFormType::class, $bet, [
            'run_targets' => $runTargets,
            'converter' => $oddsStorageDataConverter,
            'target_placeholder' => $targetPlaceholder,
            'target_expanded' => $targetExpanded,
            'target_required' => $targetRequired,
            'property_mapped' => $propertyMapped,
            'category_label' => $betCategoryLabel,
            'class_name' => $targetClassName
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bet = $form->getData();
            $amount = $bet->getAmount();
            $wallet = $user->getWallet();
            $walletAmmount = $wallet->getAmount() ?? 0;
            $newWalletAmount = intval($walletAmmount - $amount);
            //dd($bet);
            //dd($user);
            if ($newWalletAmount >= 0) {
                $wallet->setAmount($newWalletAmount);
                $teamName = ($bet->getTeam() !== null) ? $bet->getTeam()->getName() : 'Nul';
                $designation = $bet->getDesignation() . ' ' . $teamName;
                $user->addOnGoingBet($bet);
                //$dateTimeConverter = new DateTimeStorageDataConverter();
                $date = new \DateTimeImmutable("now", new \DateTimeZone(DateTimeStorageDataConverter::STORED_TIME_ZONE));
                //->setDateTimeConverter($dateTimeConverter)
                $bet
                    ->setOdds($oddsStorageDataConverter->convertOddsMultiplierToStoredData(2))
                    ->setUser($user)
                    ->setDesignation($designation)
                    ->setBetDate($date);
                //dd($bet);
                // Add success message
                $this->addFlash(
                    'success',
                    "Votre paris a été pris et validé avec succès."
                );
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($bet);
                $entityManager->flush();
            } else {
                // Add missing founds message
                $this->addFlash(
                    'warning',
                    "Vous manquez de fonds pour poser votre paris."
                );
            }
        }
        return $this->render('bet/index.html.twig', [
            'bet' => $bet,
            'form' => $form->createView()
        ]);
    }
}
