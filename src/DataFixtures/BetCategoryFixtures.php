<?php

namespace App\DataFixtures;

use App\Entity\BetCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class BetCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'name' => "result",
                'description' => null,
                'allowDraw' => true,
                'target' => "teams",
                'onCompetition' => false
            ],
            [
                'name' => "result",
                'description' => null,
                'allowDraw' => false,
                'target' => "teams",
                'onCompetition' => true
            ],
            [
                'name' => "score",
                'description' => null,
                'allowDraw' => true,
                'target' => "teams",
                'onCompetition' => false
            ],
            [
                'name' => "points",
                'description' => null,
                'allowDraw' => true,
                'target' => "teams",
                'onCompetition' => false
            ],
            [
                'name' => "goalsLine",
                'description' => null,
                'allowDraw' => false,
                'target' => "members",
                'onCompetition' => false
            ],
            [
                'name' => "toScore",
                'description' => null,
                'allowDraw' => true,
                'target' => "teams",
                'onCompetition' => false
            ],
            [
                'name' => "mostProlificHalfTime",
                'description' => null,
                'allowDraw' => true,
                'target' => "teams",
                'onCompetition' => false
            ],
            [
                'name' => "finishRace",
                'description' => null,
                'allowDraw' => false,
                'target' => "members",
                'onCompetition' => false
            ],
            [
                'name' => "finalStage",
                'description' => null,
                'allowDraw' => false,
                'target' => "members",
                'onCompetition' => false
            ],
            [
                'name' => "setsNumber",
                'description' => null,
                'allowDraw' => false,
                'target' => "members",
                'onCompetition' => false
            ],
            [
                'name' => "top3",
                'description' => null,
                'allowDraw' => false,
                'target' => "teams",
                'onCompetition' => false
            ],
            [
                'name' => "top10",
                'description' => null,
                'allowDraw' => false,
                'target' => "teams",
                'onCompetition' => false
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
                ->setOnCompetition($testData[$i]['onCompetition'])
                ;
            $manager->persist($betCategory);
        }
        $manager->flush();
    }
}
