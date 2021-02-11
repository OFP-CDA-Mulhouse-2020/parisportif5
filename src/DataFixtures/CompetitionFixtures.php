<?php

namespace App\DataFixtures;

use App\Entity\Competition;
use App\Repository\BetCategoryRepository;
use App\Repository\SportRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class CompetitionFixtures extends Fixture implements DependentFixtureInterface
{
    private SportRepository $sportRepository;
    private BetCategoryRepository $betCategoryRepository;

    public function __construct(
        SportRepository $sportRepository,
        BetCategoryRepository $betCategoryRepository
    ) {
        $this->sportRepository = $sportRepository;
        $this->betCategoryRepository = $betCategoryRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'name' => "Championnat1",
                'start' => "2021-04-01 08:00",
                'country' => "FR",
                'betCategoryName' => "result",
                'sport' => [
                    'name' => "Football",
                    'country' => "FR"
                ]
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $competitionSport = $this->sportRepository->findOneBy([
                'name' => $testData[$i]['sport']['name'],
                'country' => $testData[$i]['sport']['country']
            ]);
            $betCategory = $this->betCategoryRepository->findOneBy([
                "name" => $testData[$i]['betCategoryName']
            ]);
            $competition = new Competition();
            $competition
                ->setName($testData[$i]['name'])
                ->setCountry($testData[$i]['country'])
                ->setStartDate(new \DateTimeImmutable($testData[$i]['start'], new \DateTimeZone("UTC")))
                ->setSport($competitionSport)
                ->addBetCategory($betCategory)
                ;
            $manager->persist($competition);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SportFixtures::class,
            BetCategoryFixtures::class
        ];
    }
}
