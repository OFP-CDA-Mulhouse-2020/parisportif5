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

final class MemberFixtures extends Fixture implements DependentFixtureInterface
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

    public function getDependencies(): array
    {
        return [
            MemberRoleFixtures::class,
            MemberStatusFixtures::class,
            SportFixtures::class,
            TeamFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'first_name' => "Joueur",
                'last_name' => "Random",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'first_name' => "Machin",
                'last_name' => "Truc",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'first_name' => "Jean",
                'last_name' => "Dupont",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'first_name' => "Bidule",
                'last_name' => "Mcdonald",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'first_name' => "Pierre",
                'last_name' => "Caillou",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'first_name' => "Michel",
                'last_name' => "Bouzin",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'first_name' => "Sam",
                'last_name' => "Duplot",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'first_name' => "Luc",
                'last_name' => "Durand",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'first_name' => "Elias",
                'last_name' => "De Kelliwick",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'first_name' => "Arthur",
                'last_name' => "Pendragon",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'first_name' => "Lancelot",
                'last_name' => "Dulac",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'first_name' => "Perceval",
                'last_name' => "Legallois",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'first_name' => "Martin",
                'last_name' => "Luther",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'first_name' => "Zinédine",
                'last_name' => "Zidane",
                'country' => "FR",
                'odds' => 15000
            ],
            [
                'first_name' => "Jean",
                'last_name' => "Marsouin",
                'country' => "FR",
                'odds' => 15000
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
                ->setCountry($testData[$i]['country'])
                ->setOdds($testData[$i]['odds']);
            $manager->persist($member);
        }
        $manager->flush();
    }
}
