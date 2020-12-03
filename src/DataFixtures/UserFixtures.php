<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        for ($count = 0; $count < 20; $count++) {
            $user = new User();
            $user->setCivility("Monsieur");
            $user->setFirstName("Dupont" . $count);
            $user->setLastName("Dupond" . $count);
            $user->setEmailAddress("monsieurdupont" . $count . "@adresse.com");
            $user->setBillingAddress($count . " Rue de Champignac");
            $user->setBillingPostcode("90" . $count);
            $user->setBillingCountry("France");
            $user->setBirthDate(new DateTime("1950-11-11"));
            $user->setPassword("Test123456€");
            $user->setTimeZoneSelected("Europe/Paris");
            $manager->persist($user);
        }

        $manager->flush();
    }
}
