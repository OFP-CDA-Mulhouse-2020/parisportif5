<?php

namespace App\DataFixtures;

use App\Entity\MemberStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class MemberStatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'name' => "disqualified"
            ],
            [
                'name' => "suspended"
            ],
            [
                'name' => "injured"
            ],
            [
                'name' => "substitute"
            ],
            [
                'name' => "titular"
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $memberStatus = new MemberStatus();
            $memberStatus
                ->setName($testData[$i]['name']);
            $manager->persist($memberStatus);
        }
        $manager->flush();
    }
}
