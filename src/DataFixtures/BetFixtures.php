<?php

namespace App\DataFixtures;

use App\Service\DateTimeStorageDataConverter;
use App\Entity\Bet;
use App\Repository\BetCategoryRepository;
use App\Repository\CompetitionRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class BetFixtures extends Fixture
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
                'amount' => 1000,
                'odds' => '2',
                'betdate' => "now",
                'user' => "tintin.dupont@test.fr",
                'categoryName' => "result",
                'competition' => [
                    'name' => "Championnat1",
                    'start' => "2021-04-01 08:00",
                    'country' => "FR"
                ]
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $betCompetition = $this->competitionRepository->findOneBy([
                'name' => $testData[$i]['competition']['name'],
                'country' => $testData[$i]['competition']['country']
            ]);
            //$betCompetition = $this->getReference(CompetitionFixtures::COMPETITION_OBJECT);
            $betCategory = $this->betCategoryRepository->findOneBy([
                "name" => $testData[$i]['categoryName']
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
            LanguageFixtures::class,
            UserFixtures::class,
            BetCategoryFixtures::class,
            SportFixtures::class,
            CompetitionFixtures::class,
            TeamFixtures::class,
            MemberRoleFixtures::class,
            MemberStatusFixtures::class,
            MemberFixtures::class,
            LocationFixtures::class,
            RunFixtures::class
        ];
    }
}
