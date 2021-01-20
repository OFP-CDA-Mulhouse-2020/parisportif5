<?php

namespace App\DataFixtures;

use App\Entity\Sport;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SportFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'name' => "Football",
                'country' => "FR",
                'run_type' => "fixture",
                'individual_type' => false,
                'collective_type' => true,
                'min_teams_by_run' => 1,
                'max_teams_by_run' => 2,
                'min_members_by_team' => 11,
                'max_members_by_team' => 15
            ],
            [
                'name' => "Handball",
                'country' => "FR",
                'run_type' => "fixture",
                'individual_type' => false,
                'collective_type' => true,
                'min_teams_by_run' => 1,
                'max_teams_by_run' => 2,
                'min_members_by_team' => 7,
                'max_members_by_team' => 12
            ],
            [
                'name' => "Formule1",
                'country' => "FR",
                'run_type' => "race",
                'individual_type' => true,
                'collective_type' => true,
                'min_teams_by_run' => 1,
                'max_teams_by_run' => false,
                'min_members_by_team' => 1,
                'max_members_by_team' => 2
            ],
            [
                'name' => "Tennis",
                'country' => "FR",
                'run_type' => "fixture",
                'individual_type' => true,
                'collective_type' => false,
                'min_teams_by_run' => 1,
                'max_teams_by_run' => 2,
                'min_members_by_team' => 1,
                'max_members_by_team' => 1
            ],
            [
                'name' => "Tennis de table",
                'country' => "FR",
                'run_type' => "fixture",
                'individual_type' => true,
                'collective_type' => false,
                'min_teams_by_run' => 1,
                'max_teams_by_run' => 2,
                'min_members_by_team' => 1,
                'max_members_by_team' => 1
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $sport = new Sport();
            $sport
                ->setName($testData[$i]['name'])
                ->setCountry($testData[$i]['country'])
                ->setRunType($testData[$i]['run_type'])
                ->setIndividualType($testData[$i]['individual_type'])
                ->setCollectiveType($testData[$i]['collective_type'])
                ->setMinTeamsByRun($testData[$i]['min_teams_by_run'])
                ->setMaxTeamsByRun($testData[$i]['max_teams_by_run'])
                ->setMinMembersByTeam($testData[$i]['min_members_by_team'])
                ->setMaxMembersByTeam($testData[$i]['max_members_by_team'])
                ;
            $manager->persist($sport);
        }
        $manager->flush();
    }
}
