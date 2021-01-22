<?php

namespace App\DataFixtures;

use App\Entity\Member;
use App\Entity\MemberRole;
use App\Entity\MemberStatus;
use App\Entity\Team;
use App\Repository\MemberRoleRepository;
use App\Repository\MemberStatusRepository;
use App\Repository\TeamRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MemberFixtures extends Fixture implements DependentFixtureInterface
{
    private TeamRepository $teamRepository;
    private MemberRoleRepository $memberRoleRepository;
    private MemberStatusRepository $memberStatusRepository;

    public function __construct(
        TeamRepository $teamRepository,
        MemberStatusRepository $memberStatusRepository,
        MemberRoleRepository $memberRoleRepository
    ) {
        $this->teamRepository = $teamRepository;
        $this->memberStatusRepository = $memberStatusRepository;
        $this->memberRoleRepository = $memberRoleRepository;
    }

    public function getDependencies()
    {
        return array(
            MemberRoleFixtures::class,
            MemberStatusFixtures::class,
            SportFixtures::class,
            TeamFixtures::class
        );
    }

    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'first_name' => "Joueur",
                'last_name' => "Random",
                'country' => "FR"
            ],
            [
                'first_name' => "Machin",
                'last_name' => "Truc",
                'country' => "FR"
            ],
            [
                'first_name' => "Jean",
                'last_name' => "Dupont",
                'country' => "FR"
            ],
            [
                'first_name' => "Bidule",
                'last_name' => "Mcdonald",
                'country' => "FR"
            ],
            [
                'first_name' => "Pierre",
                'last_name' => "Caillou",
                'country' => "FR"
            ],
            [
                'first_name' => "Michel",
                'last_name' => "Bouzin",
                'country' => "FR"
            ],
            [
                'first_name' => "Sam",
                'last_name' => "Duplot",
                'country' => "FR"
            ],
            [
                'first_name' => "Luc",
                'last_name' => "Durand",
                'country' => "FR"
            ],
            [
                'first_name' => "Elias",
                'last_name' => "De Kelliwick",
                'country' => "FR"
            ],
            [
                'first_name' => "Arthur",
                'last_name' => "Pendragon",
                'country' => "FR"
            ],
            [
                'first_name' => "Lancelot",
                'last_name' => "Dulac",
                'country' => "FR"
            ],
            [
                'first_name' => "Perceval",
                'last_name' => "Legallois",
                'country' => "FR"
            ],
            [
                'first_name' => "Martin",
                'last_name' => "Luther",
                'country' => "FR"
            ],
            [
                'first_name' => "Zinédine",
                'last_name' => "Zidane",
                'country' => "FR"
            ],
            [
                'first_name' => "Jean",
                'last_name' => "Marsouin",
                'country' => "FR"
            ],
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $member = new Member();
            $status = $this->memberStatusRepository->findOneBy(['name' => "titular"]);
            $role = $this->memberRoleRepository->findOneBy(['name' => "footballer"]);
            $team = $this->teamRepository->findOneBy(['name' => "Racing Club de Strasbourg Alsace"]);
            $member
                ->setMemberRole($role)
                ->setMemberStatus($status)
                ->setTeam($team)
                ->setFirstName($testData[$i]['first_name'])
                ->setLastName($testData[$i]['last_name'])
                ->setCountry($testData[$i]['country']);
                ;
            $manager->persist($member);
        }
        $manager->flush();
    }
}
