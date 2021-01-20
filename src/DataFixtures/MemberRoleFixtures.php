<?php

namespace App\DataFixtures;

use App\Entity\MemberRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MemberRoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'name' => "player"
            ],
            [
                'name' => "pilot"
            ],
            [
                'name' => "co-pilote"
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $memberRole = new MemberRole();
            $memberRole
                ->setName($testData[$i]['name']);
            $manager->persist($memberRole);
        }
        $manager->flush();
    }
}
