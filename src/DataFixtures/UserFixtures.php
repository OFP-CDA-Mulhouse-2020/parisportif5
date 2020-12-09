<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'civility' => "Monsieur",
                'firstname' => "Tintin",
                'lastname' => "Dupont",
                'address' => "1 avenue Hergé",
                'city' => "COLMAR",
                'postcode' => "68000",
                'country' => "FR",
                'email' => "tintin.dupont@test.fr",
                'password' => "@Hadock5",
                'birthdate' => "2000-10-20",
                'timezone' => "Europe/Paris"
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $user = new User();
            $user
                ->setCivility($testData[$i]['civility'])
                ->setFirstName($testData[$i]['firstname'])
                ->setLastName($testData[$i]['lastname'])
                ->setBillingAddress($testData[$i]['address'])
                ->setBillingCity($testData[$i]['city'])
                ->setBillingPostcode($testData[$i]['postcode'])
                ->setBillingCountry($testData[$i]['country'])
                ->setBirthDate(new \DateTime($testData[$i]['birthdate'], new \DateTimeZone("UTC")))
                ->setTimeZoneSelected($testData[$i]['timezone'])
                ->setEmail($testData[$i]['email'])
                ->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    $testData[$i]['password']
                ));
            $manager->persist($user);
        }
        $manager->flush();
    }
}
