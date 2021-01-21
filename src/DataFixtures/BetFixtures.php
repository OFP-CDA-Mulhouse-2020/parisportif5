<?php

namespace App\DataFixtures;

use App\Entity\Bet;
use App\Entity\Run;
use App\Repository\BetCategoryRepository;
use App\Repository\CompetitionRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BetFixtures extends Fixture
{
    private UserRepository $userRepository;
    private CompetitionRepository $competitionRepository;
    private BetCategoryRepository $betCategoryRepository;

    public function __construct(
        UserRepository $userRepository,
        CompetitionRepository $competitionRepository,
        BetCategoryRepository $betCategoryRepository
    ) {
        $this->competitionRepository = $competitionRepository;
        $this->userRepository = $userRepository;
        $this->betCategoryRepository = $betCategoryRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'designation' => "Mise 1",
                'amount' => 10,
                'odds' => 2,
                'user' => "tintin.dupont@test.fr",
                'categoryName' => "result",
                'competition' => [
                    'name' => "Championnat",
                    'start' => "2021-01-01 08:00",
                    'end' => "2021-01-10 20:00",
                    'country' => "FR"
                ]
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $betCompetition = $this->competitionRepository->findOneBy([
                'name' => $testData[$i]['competition']['name'],
                'startDate' => new \DateTimeImmutable($testData[$i]['competition']['start'], new \DateTimeZone("UTC")),
                'endDate' => new \DateTimeImmutable($testData[$i]['competition']['end'], new \DateTimeZone("UTC")),
                'country' => $testData[$i]['competition']['country']
            ]);
            $betUser = $this->userRepository->findOneByEmail($testData[$i]['user']);
            $betCategory = $this->betCategoryRepository->findOneBy([
                "name" => $testData[$i]['categoryName']
            ]);
            $bet = new Bet();
            $bet
                ->setDesignation($testData[$i]['designation'])
                ->setAmount($testData[$i]['amount'])
                ->setOdds($testData[$i]['odds'])
                ->setCompetition($betCompetition)
                ->setUser($betUser)
                ->setBetCategory($betCategory);
            $manager->persist($bet);
        }
        $manager->flush();
    }

    /** @return String[] */
    public function getDependencies(): array
    {
        return array(
            CompetitionFixtures::class,
            UserFixtures::class,
            BetCategoryFixtures::class,
            RunFixtures::class,
            TeamFixtures::class,
            MemberFixtures::class
        );
    }
}
