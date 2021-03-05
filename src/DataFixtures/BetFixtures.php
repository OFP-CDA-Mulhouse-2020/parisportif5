<?php

namespace App\DataFixtures;

use App\Entity\Bet;
use App\Repository\BetCategoryRepository;
use App\Repository\CompetitionRepository;
use App\Repository\RunRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class BetFixtures extends Fixture implements DependentFixtureInterface
{
    private UserRepository $userRepository;
    private CompetitionRepository $competitionRepository;
    private BetCategoryRepository $betCategoryRepository;
    private RunRepository $runRepository;
    private TeamRepository $teamRepository;

    public function __construct(
        UserRepository $userRepository,
        CompetitionRepository $competitionRepository,
        BetCategoryRepository $betCategoryRepository,
        RunRepository $runRepository,
        TeamRepository $teamRepository
    ) {
        $this->competitionRepository = $competitionRepository;
        $this->userRepository = $userRepository;
        $this->betCategoryRepository = $betCategoryRepository;
        $this->teamRepository = $teamRepository;
        $this->runRepository = $runRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'designation' => "Paris test",
                'amount' => 10000,
                'odds' => '2',
                'betdate' => "now",
                'user' => "tintin.dupont@test.fr",
                'category' => [
                    'name' => "result",
                    'onCompetition' => false
                ],
                'competition' => [
                    'name' => "Championnat1",
                    'start' => "2021-04-01 08:00",
                    'country' => "FR"
                ],
                'runName' => "Match 1 vs2",
                'teamName' => "Racing Club de Strasbourg Alsace"
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $betCompetition = $this->competitionRepository->findOneBy([
                'name' => $testData[$i]['competition']['name'],
                'country' => $testData[$i]['competition']['country']
            ]);
            $betCategory = $this->betCategoryRepository->findOneBy([
                "name" => $testData[$i]['category']['name'],
                "onCompetition" => $testData[$i]['category']['onCompetition']
            ]);
            $betRun = $this->runRepository->findOneBy([
                "name" => $testData[$i]['runName']
            ]);
            $betTeam = $this->teamRepository->findOneBy([
                "name" => $testData[$i]['teamName']
            ]);
            $betUser = $this->userRepository->findOneByEmail($testData[$i]['user']);
            if (!is_null($betCompetition) && !is_null($betUser) && !is_null($betCategory)) {
                $bet = new Bet();
                $bet
                    ->setDesignation($testData[$i]['designation'])
                    ->setAmount($testData[$i]['amount'])
                    ->setOdds($testData[$i]['odds'])
                    ->setCompetition($betCompetition)
                    ->setUser($betUser)
                    ->setBetCategory($betCategory)
                    ->setRun($betRun)
                    ->setTeam($betTeam)
                    ->setBetDate(new \DateTimeImmutable($testData[$i]['betdate'], new \DateTimeZone("UTC")))
                    ;
                $manager->persist($bet);
            }
        }
        $manager->flush();
    }

    /** @return String[] */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            BetCategoryFixtures::class,
            CompetitionFixtures::class,
            TeamFixtures::class,
            RunFixtures::class
        ];
    }
}
