<?php

namespace App\DataFixtures;

use App\Entity\BetSaved;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class BetSavedFixtures extends Fixture implements DependentFixtureInterface
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'designation' => "Paris test",
                'amount' => 10000,
                'odds' => '2',
                'gains' => 20000,
                'winning' => true,
                'betdate' => "now",
                'user' => "tintin.dupont@test.fr",
                'categoryName' => "result",
                'competitionName' => "Championnat1",
                'competitionStart' => "2021-04-01 08:00",
                'competitionCountry' => "FR",
                'competitionSportName' => "Football",
                'competitionSportCountry' => "FR",
                'runName' => "Match 1 vs2",
                'runEvent' => "Pool 1",
                'runStart' => "2021-04-01 08:00",
                'teamName' => "Racing Club de Strasbourg Alsace",
                'teamCountry' => "FR",
                'memberLastName' => null,
                'memberFirstName' => null,
                'memberCountry' => null
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $betUser = $this->userRepository->findOneByEmail($testData[$i]['user']);
            $bet = new BetSaved();
            $bet
                ->setDesignation($testData[$i]['designation'])
                ->setAmount($testData[$i]['amount'])
                ->setOdds($testData[$i]['odds'])
                ->setIsWinning($testData[$i]['winning'])
                ->setGains($testData[$i]['gains'])
                ->setBetDate(new \DateTimeImmutable($testData[$i]['betdate'], new \DateTimeZone("UTC")))
                ->setUser($betUser)
                ->setBetCategoryName($testData[$i]['categoryName'])
                ->setCompetitionName($testData[$i]['competitionName'])
                ->setCompetitionCountry($testData[$i]['competitionCountry'])
                ->setCompetitionStartDate(new \DateTimeImmutable($testData[$i]['competitionStart'], new \DateTimeZone("UTC")))
                ->setCompetitionSportName($testData[$i]['competitionSportName'])
                ->setCompetitionSportCountry($testData[$i]['competitionSportCountry'])
                ->setRunName($testData[$i]['runName'])
                ->setRunEvent($testData[$i]['runEvent'])
                ->setRunStartDate(new \DateTimeImmutable($testData[$i]['runStart'], new \DateTimeZone("UTC")))
                ->setTeamName($testData[$i]['teamName'])
                ->setTeamCountry($testData[$i]['teamCountry'])
                ->setMemberCountry($testData[$i]['memberCountry'])
                ->setMemberFirstName($testData[$i]['memberFirstName'])
                ->setMemberLastName($testData[$i]['memberLastName'])
                ;
            $manager->persist($bet);
        }
        $manager->flush();
    }

    /** @return String[] */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}
