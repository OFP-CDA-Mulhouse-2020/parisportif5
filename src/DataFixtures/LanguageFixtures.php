<?php

namespace App\DataFixtures;

use App\Entity\Language;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class LanguageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $testData = [
            [
                'name' => 'anglais',
                'country' => 'Uinted Kingdom',
                'code' => 'en_GB',
                'dateFormat' => 'd-m-Y',
                'timeFormat' => 'H:i:s',
                'timezone' => 'Europe/London'
            ],
            [
                'name' => 'allemand',
                'country' => 'Deutschland',
                'code' => 'de_DE',
                'dateFormat' => 'd/m/Y',
                'timeFormat' => 'H:i:s',
                'timezone' => 'Europe/Berlin'
            ],
            [
                'name' => 'français',
                'country' => 'France',
                'code' => 'fr_FR',
                'dateFormat' => 'd/m/Y',
                'timeFormat' => 'H:i:s',
                'timezone' => 'Europe/Paris'
            ]
        ];
        $count = count($testData);
        for ($i = 0; $i < $count; $i++) {
            $language = new Language();
            $language
                ->setName($testData[$i]['name'])
                ->setCountry($testData[$i]['country'])
                ->setCode($testData[$i]['code'])
                ->setDateFormat($testData[$i]['dateFormat'])
                ->setTimeFormat($testData[$i]['timeFormat'])
                ->setCapitalTimeZone($testData[$i]['timezone']);
            $manager->persist($language);
        }
        $manager->flush();
    }
}
