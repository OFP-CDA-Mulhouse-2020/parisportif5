<?php

namespace App\DataFixtures;

use App\Entity\BetCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BetCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'name' => "result",
                'description' => null
            ],
            [
                'name' => "score",
                'description' => null
            ],
            [
                'name' => "points",
                'description' => null
            ],
            [
                'name' => "goalsLine",
                'description' => null
            ],
            [
                'name' => "toScore",
                'description' => null
            ],
            [
                'name' => "mostProlificHalfTime",
                'description' => null
            ],
            [
                'name' => "finishRace",
                'description' => null
            ],
            [
                'name' => "finalStage",
                'description' => null
            ],
            [
                'name' => "setsNumber",
                'description' => null
            ],
            [
                'name' => "top3",
                'description' => null
            ],
            [
                'name' => "top10",
                'description' => null
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $betCategory = new BetCategory();
            $betCategory
                ->setName($testData[$i]['name'])
                ->setDescription($testData[$i]['description']);
            $manager->persist($betCategory);
        }
        $manager->flush();
    }
}
