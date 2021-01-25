<?php

namespace App\DataFixtures;

use App\Entity\Sport;
use App\Entity\Team;
use App\Repository\SportRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TeamFixtures extends Fixture
{
    private SportRepository $sportRepository;

    public function __construct(SportRepository $sportRepository)
    {
        $this->sportRepository = $sportRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'name' => "Racing Club de Strasbourg Alsace",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'name' => "Paris Saint-Germain",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'name' => "AS Saint-Étienne",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'name' => "Olympique de Marseille",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'name' => "Olympique Lyonnais",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'name' => "Stade Brestois 29",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'name' => "FC Metz",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'name' => "Girondins de Bordeaux",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'name' => "Lille OSC",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'name' => "Stade Rennais",
                'country' => "FR",
                'odds' => 15000
            ],
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $team = new Team();
            $foot = $this->sportRepository->findOneBy(['name' => "Football"]);
            $team
                ->setSport($foot)
                ->setName($testData[$i]['name'])
                ->setCountry($testData[$i]['country'])
                ->setOdds($testData[$i]['odds']);
            $manager->persist($team);
        }
        $manager->flush();
    }
}
