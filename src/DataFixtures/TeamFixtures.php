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
                'country' => "FR"
            ],
            [
                'name' => "Paris Saint-Germain",
                'country' => "FR"
            ],
            [
                'name' => "AS Saint-Étienne",
                'country' => "FR"
            ],
            [
                'name' => "Olympique de Marseille",
                'country' => "FR"
            ],
            [
                'name' => "Olympique Lyonnais",
                'country' => "FR"
            ],
            [
                'name' => "Stade Brestois 29",
                'country' => "FR"
            ],
            [
                'name' => "FC Metz",
                'country' => "FR"
            ],
            [
                'name' => "Girondins de Bordeaux",
                'country' => "FR"
            ],
            [
                'name' => "Lille OSC",
                'country' => "FR"
            ],
            [
                'name' => "Stade Rennais",
                'country' => "FR"
            ],
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $team = new Team();
            $foot = $this->sportRepository->findOneBy(['name' => "Football"]);
            $team
                ->setSport($foot)
                ->setName($testData[$i]['name'])
                ->setCountry($testData[$i]['country']);
            $manager->persist($team);
        }
        $manager->flush();
    }
}
