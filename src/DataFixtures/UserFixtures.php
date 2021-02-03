<?php

namespace App\DataFixtures;

use App\DataConverter\DateTimeStorageDataConverter;
use App\Entity\Language;
use App\Entity\User;
use App\Entity\Wallet;
use App\Repository\LanguageRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordEncoderInterface $passwordEncoder;
    private LanguageRepository $languageRepository;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        LanguageRepository $languageRepository
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->languageRepository = $languageRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $testData = [
            /*[
                'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
                'civility' => null,
                'firstname' => "Admin1",
                'lastname' => "Admin1",
                'address' => "admin1",
                'city' => "COLMAR",
                'postcode' => "68000",
                'country' => "FR",
                'email' => "admin1@admin.fr",
                'verified' => true,
                'password' => "administrateur1",
                'birthdate' => "2000-10-20",
                'timezone' => "Europe/Paris",
                'newsletters' => false,
                'identityDocument' => "identity_card.pdf",
                'residenceProof' => "invoice.jpg",
                'language' => 'fr_FR'
            ],*/
            [
                'roles' => ['ROLE_USER, ROLE_ADMIN'],
                'civility' => "Monsieur",
                'firstname' => "Tintin",
                'lastname' => "Dupont",
                'address' => "1 avenue Herger",
                'city' => "COLMAR",
                'postcode' => "68000",
                'country' => "FR",
                'email' => "tintin.dupont@test.fr",
                'verified' => true,
                'password' => "@Hadock5",
                'birthdate' => "2000-10-20",
                'timezone' => "Europe/Paris",
                'newsletters' => false,
                'identityDocument' => "identity_card.pdf",
                'residenceProof' => "invoice.jpg",
                'language' => 'fr_FR'
            ]/*,
            [
                'roles' => ['ROLE_USER'],
                'civility' => "Monsieur",
                'firstname' => "Toto",
                'lastname' => "Dupond",
                'address' => "3 avenue Herger",
                'city' => "COLMAR",
                'postcode' => "68000",
                'country' => "FR",
                'email' => "toto.dupond@test.fr",
                'verified' => true,
                'password' => "@Hadock123",
                'birthdate' => "2000-11-21",
                'timezone' => "Europe/Paris",
                'newsletters' => false,
                'identityDocument' => "identity_card.pdf",
                'residenceProof' => "invoice.jpg",
                'language' => 'fr_FR'
            ]*/
        ];
        $count = count($testData);
        $converter = new DateTimeStorageDataConverter();
        for ($i = 0; $i < $count; $i++) {
            $user = new User($converter);
            $userWallet = new Wallet();
            $userLanguage = $this->languageRepository->findOneByLanguageCode($testData[$i]['language']);
            if (is_null($userLanguage)) {
                $userLanguage = $this->languageRepository->languageByDefault();
            }
            $user
                ->setDateTimeConverter($converter)
                ->setRoles(['ROLE_USER'])
                ->setCivility($testData[$i]['civility'])
                ->setFirstName($testData[$i]['firstname'])
                ->setLastName($testData[$i]['lastname'])
                ->setBillingAddress($testData[$i]['address'])
                ->setBillingCity($testData[$i]['city'])
                ->setBillingPostcode($testData[$i]['postcode'])
                ->setBillingCountry($testData[$i]['country'])
                ->setBirthDate(new \DateTimeImmutable($testData[$i]['birthdate'], new \DateTimeZone("UTC")))
                ->setTimeZoneSelected($testData[$i]['timezone'])
                ->setIsVerified($testData[$i]['verified'])
                ->setRoles($testData[$i]['roles'])
                ->setEmail($testData[$i]['email'])
                ->setNewsletters($testData[$i]['newsletters'])
                ->setResidenceProof($testData[$i]['residenceProof'])
                ->setIdentityDocument($testData[$i]['identityDocument'])
                ->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    $testData[$i]['password']
                ))
                ->setWallet($userWallet)
                ->setLanguage($userLanguage);
            $userWallet
                ->setUser($user)
                ->setAmount(20000);
            $manager->persist($user);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            LanguageFixtures::class
        ];
    }
}
