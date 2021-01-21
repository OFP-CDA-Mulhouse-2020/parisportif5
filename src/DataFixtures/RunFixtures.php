<?php

namespace App\DataFixtures;

use App\Entity\Run;
use App\Repository\CompetitionRepository;
use App\Repository\LocationRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RunFixtures extends Fixture implements DependentFixtureInterface
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
                'start' => "2021-01-01 08:00",
                'end' => "2021-01-01 20:00",
                'noWinner' => null,
                'competition' => [
                    'name' => "Championnat",
                    'start' => "2021-01-01 08:00",
                    'end' => "2021-01-10 20:00",
                    'country' => "FR"
                ],
                'location' => [
                    'place' => "ALPES",
                    'country' => "FR"
                ]
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $runCompetition = $this->competitionRepository->findOneBy([
                'name' => $testData[$i]['competition']['name'],
                'startDate' => new \DateTimeImmutable($testData[$i]['competition']['start'], new \DateTimeZone("UTC")),
                'endDate' => new \DateTimeImmutable($testData[$i]['competition']['end'], new \DateTimeZone("UTC")),
                'country' => $testData[$i]['competition']['country']
            ]);
            $runLocation = $this->locationRepository->findOneBy([
                "place" => $testData[$i]['location']['place'],
                "country" => $testData[$i]['location']['country']
            ]);
            //$runTeams
            //$runScores
            $run = new Run();
            $run
                ->setName($testData[$i]['name'])
                ->setEvent($testData[$i]['event'])
                ->setStartDate(new \DateTimeImmutable($testData[$i]['start'], new \DateTimeZone("UTC")))
                ->setEndDate(new \DateTimeImmutable($testData[$i]['end'], new \DateTimeZone("UTC")))
                ->setNoWinner($testData[$i]['noWinner'])
                ->setCompetition($runCompetition)
                ->setLocation($runLocation);
            $manager->persist($run);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return array(
            CompetitionFixtures::class,
            LocationFixtures::class
        );
    }
}
