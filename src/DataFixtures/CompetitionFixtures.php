<?php

namespace App\DataFixtures;

use App\Entity\Competition;
use App\Repository\BetCategoryRepository;
use App\Repository\SportRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CompetitionFixtures extends Fixture implements DependentFixtureInterface
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
                'name' => "Championnat",
                'start' => "2021-01-01 08:00",
                'end' => "2021-01-10 20:00",
                'betCategoryName' => "result",
                'sport' => [
                    'name' => "foot",
                    'runType' => "fixture",
                    'country' => "FR"
                ]
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $competitionSport = $this->sportRepository->findOneBy([
                'name' => $testData[$i]['sport']['name'],
                'runType' => $testData[$i]['sport']['runType'],
                'country' => $testData[$i]['sport']['country']
            ]);
            $betCategory = $this->betCategoryRepository->findOneBy([
                "name" => $testData[$i]['betCategoryName']
            ]);
            $competition = new Competition();
            $competition
                ->setName($testData[$i]['name'])
                ->setStartDate(new \DateTimeImmutable($testData[$i]['start'], new \DateTimeZone("UTC")))
                ->setEndDate(new \DateTimeImmutable($testData[$i]['end'], new \DateTimeZone("UTC")))
                ->setSport($competitionSport)
                ->addBetCategory($betCategory);
            $manager->persist($competition);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return array(
            SportFixtures::class,
            BetCategoryFixtures::class
        );
    }
}
