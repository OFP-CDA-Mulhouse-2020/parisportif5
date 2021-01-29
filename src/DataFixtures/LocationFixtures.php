<?php

namespace App\DataFixtures;

use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class LocationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'place' => "VOSGES",
                'country' => "FR",
                'timezone' => "Europe/Paris"
            ],
            [
                'place' => "ALPES",
                'country' => "FR",
                'timezone' => "Europe/Paris"
            ],
            [
                'place' => "JURAS",
                'country' => "FR",
                'timezone' => "Europe/Paris"
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $location = new Location();
            $location
                ->setCountry($testData[$i]['country'])
                ->setPlace($testData[$i]['place'])
                ->setTimeZone($testData[$i]['timezone']);
            $manager->persist($location);
        }
        $manager->flush();
    }
}
