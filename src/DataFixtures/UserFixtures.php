<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        
        for ($count = 0; $count < 20; $count++) {
            $user = new User();
            $user->setCivility("Civilité : " . $count . "Monsieur");
            $user->setFirstName("Prénom : " . "Dupont" . $count);
            $user->setLastName("Nom de Famille : " . "Dupond" . $count);
            $user->setBillingAddress("Adresse : " . $count . " Rue de Champignac");
            $user->setBillingPostcode("Code postal : 90" . $count);
            $user->setBillingCountry("Pays : " .$count);
            $user->setBirthDate(new DateTime());
            $manager->persist($user);
        }

        $manager->flush();
    }
}
