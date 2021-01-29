<?php

namespace App\DataFixtures;

use App\Service\DateTimeStorageDataConverter;
use App\Entity\Run;
use App\Repository\CompetitionRepository;
use App\Repository\LocationRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class RunFixtures extends Fixture implements DependentFixtureInterface
{
    private LocationRepository $locationRepository;
    private CompetitionRepository $competitionRepository;

    public function __construct(
        LocationRepository $locationRepository,
        CompetitionRepository $competitionRepository
    ) {
        $this->competitionRepository = $competitionRepository;
        $this->locationRepository = $locationRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'name' => "Match 1 vs2",
                'event' => "pool 1",
                'start' => "2021-02-01 09:00",
                'end' => "2021-02-01 20:00",
                'competition' => [
                    'name' => "Championnat1",
                    'start' => "2021-02-01 08:00",
                    'end' => "2021-02-10 20:00",
                    'country' => "FR"
                ],
                'location' => [
                    'place' => "ALPES",
                    'country' => "FR"
                ]
            ]
        ];
        $count = count($testData);
        $converter = new DateTimeStorageDataConverter();
        for ($i = 0; $i < $count; $i++) {
            $runCompetition = $this->competitionRepository->findOneBy([
                'name' => $testData[$i]['competition']['name'],
                'country' => $testData[$i]['competition']['country']
            ]);
            $runLocation = $this->locationRepository->findOneBy([
                "place" => $testData[$i]['location']['place'],
                "country" => $testData[$i]['location']['country']
            ]);
            //$runTeams
            //$runScores
            $run = new Run($converter);
            $run
                ->setDateTimeConverter($converter)
                ->setName($testData[$i]['name'])
                ->setEvent($testData[$i]['event'])
                ->setStartDate(new \DateTimeImmutable($testData[$i]['start'], new \DateTimeZone("UTC")))
                ->setEndDate(new \DateTimeImmutable($testData[$i]['end'], new \DateTimeZone("UTC")))
                ->setCompetition($runCompetition)
                ->setLocation($runLocation)
                ;
            $manager->persist($run);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CompetitionFixtures::class,
            LocationFixtures::class
        ];
    }
}
