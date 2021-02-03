<?php

namespace App\DataFixtures;

use App\Entity\Run;
use App\Repository\TeamRepository;
use App\Repository\LocationRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\CompetitionRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Service\DateTimeStorageDataConverter;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

final class RunFixtures extends Fixture implements DependentFixtureInterface
{
    private LocationRepository $locationRepository;
    private CompetitionRepository $competitionRepository;
    private TeamRepository $teamRepository;

    public function __construct(
        LocationRepository $locationRepository,
        CompetitionRepository $competitionRepository,
        TeamRepository $teamRepository
    ) {
        $this->competitionRepository = $competitionRepository;
        $this->locationRepository = $locationRepository;
        $this->teamRepository = $teamRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'name' => "Match 1 vs2",
                'event' => "pool 1",
                'start' => "2021-04-01 09:00",
                'end' => "2021-04-01 20:00",
                'competition' => [
                    'name' => "Championnat1",
                    'start' => "2021-04-01 08:00",
                    'end' => "2021-04-10 20:00",
                    'country' => "FR"
                ],
                'location' => [
                    'place' => "ALPES",
                    'country' => "FR"
                ],
                'teams' => [
                    "Girondins de Bordeaux",
                    "Racing Club de Strasbourg Alsace"
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
            if (!empty($testData[$i]['teams']) && count($testData[$i]['teams']) === 2) {
                $resultA = $this->teamRepository->findBy(["name" => $testData[$i]['teams'][0]], null, 1);
                $teamA = $resultA[0];
                $run->addTeam($teamA);
                $resultB = $this->teamRepository->findBy(["name" => $testData[$i]['teams'][1]], null, 1);
                $teamB = $resultB[0];
                $run->addTeam($teamB);
            }
            $manager->persist($run);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CompetitionFixtures::class,
            LocationFixtures::class,
            TeamFixtures::class
        ];
    }
}
