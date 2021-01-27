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
                'description' => null,
                'allowDraw' => true,
                'target' => "teams"
            ],
            [
                'name' => "score",
                'description' => null,
                'allowDraw' => true,
                'target' => "teams"
            ],
            [
                'name' => "points",
                'description' => null,
                'allowDraw' => true,
                'target' => "teams"
            ],
            [
                'name' => "goalsLine",
                'description' => null,
                'allowDraw' => false,
                'target' => "members"
            ],
            [
                'name' => "toScore",
                'description' => null,
                'allowDraw' => true,
                'target' => "teams"
            ],
            [
                'name' => "mostProlificHalfTime",
                'description' => null,
                'allowDraw' => true,
                'target' => "teams"
            ],
            [
                'name' => "finishRace",
                'description' => null,
                'allowDraw' => false,
                'target' => "members"
            ],
            [
                'name' => "finalStage",
                'description' => null,
                'allowDraw' => false,
                'target' => "members"
            ],
            [
                'name' => "setsNumber",
                'description' => null,
                'allowDraw' => false,
                'target' => "members"
            ],
            [
                'name' => "top3",
                'description' => null,
                'allowDraw' => false,
                'target' => "teams"
            ],
            [
                'name' => "top10",
                'description' => null,
                'allowDraw' => false,
                'target' => "teams"
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $betCategory = new BetCategory();
            $betCategory
                ->setName($testData[$i]['name'])
                ->setDescription($testData[$i]['description'])
                ->setAllowDraw($testData[$i]['allowDraw'])
                ->setTarget($testData[$i]['target'])
                ;
            $manager->persist($betCategory);
        }
        $manager->flush();
    }
}
