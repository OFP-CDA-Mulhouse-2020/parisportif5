<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Language;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Language
 */
final class LanguageTest extends WebTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidLanguage(): Language
    {
        $language = new Language();
        $language
            ->setName('anglais US')
            ->setCountry('USA')
            ->setCode('en_US')
            ->setDateFormat('Y-m-d')
            ->setTimeFormat('H:i:s')
            ->setCapitalTimeZone('America/New_York');
        return $language;
    }

    /**
     * @dataProvider nameUncompatibleProvider
     */
    public function testNameUncompatible(string $name): void
    {
        $language = $this->createValidLanguage();
        $language->setName($name);
        $violations = $this->validator->validate($language);
        $this->assertCount(1, $violations);
    }

    public function nameUncompatibleProvider(): array
    {
        return [
            ["l'ancien Français"],
            ["Anglais1"],
            [""],
            ["    "],
            ["italien-sicile"]
        ];
    }

    /**
     * @dataProvider nameCompatibleProvider
     */
    public function testNameCompatible(string $name): void
    {
        $language = $this->createValidLanguage();
        $language->setName($name);
        $violations = $this->validator->validate($language);
        $this->assertCount(0, $violations);
    }

    public function nameCompatibleProvider(): array
    {
        return [
            ["Français"],
            ["anglais"],
            ["Créole Réunionnais"]
        ];
    }

        /**
     * @dataProvider countryUncompatibleProvider
     */
    public function testCountryUncompatible(string $country): void
    {
        $language = $this->createValidLanguage();
        $language->setCountry($country);
        $violations = $this->validator->validate($language);
        $this->assertCount(1, $violations);
    }

    public function countryUncompatibleProvider(): array
    {
        return [
            ["Grande-Bretagne"],
            ["France1"],
            [""],
            ["    "],
            ["l'apostrophe"]
        ];
    }

    /**
     * @dataProvider countryCompatibleProvider
     */
    public function testCountryCompatible(string $country): void
    {
        $language = $this->createValidLanguage();
        $language->setCountry($country);
        $violations = $this->validator->validate($language);
        $this->assertCount(0, $violations);
    }

    public function countryCompatibleProvider(): array
    {
        return [
            ["Françe"],
            ["italie"],
            ["Grande Bretagne"]
        ];
    }

     /**
     * @dataProvider codeCompatibleProvider
     * Identifiants locaux au format ICU
     */
    public function testCodeCompatible(string $code): void
    {
        $language = $this->createValidLanguage();
        $language->setCode($code);
        $violations = $this->validator->validate($language);
        $this->assertCount(0, $violations);
    }

    public function codeCompatibleProvider(): array
    {
        return [
            ["es_ES"],
            ["FRA"],
            ["FR"],
            ["FRa_fr"],
            ["fr-Fr"]
        ];
    }

    /**
     * @dataProvider codeUncompatibleProvider
     */
    public function testCodeUncompatible(string $code): void
    {
        $language = $this->createValidLanguage();
        $language->setCode($code);
        $violations = $this->validator->validate($language);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function codeUncompatibleProvider(): array
    {
        return [
            ["XY"],
            ["Français"],
            ["France"],
            [''],
            ['   ']
        ];
    }

    /**
     * @dataProvider dateFormatCompatibleProvider
     */
    public function testDateFormatCompatible(string $dateFormat): void
    {
        $language = $this->createValidLanguage();
        $language->setDateFormat($dateFormat);
        $violations = $this->validator->validate($language);
        $this->assertCount(0, $violations);
    }

    public function dateFormatCompatibleProvider(): array
    {
        return [
            ["d/m/Y"],
            ["l, Y-m-d "],
            ["D, m d Y"],
            ["j n Y"]
        ];
    }

    /**
     * @dataProvider dateFormatUncompatibleProvider
     * Liste des interdits : Symbole monétaire et mathématique,
     * chiffre en exposant ou en indice, guillemet français
     * les caractères de contrôle invisibles et les points de code non utilisés
     */
    public function testDateFormatUncompatible(string $dateFormat): void
    {
        $language = $this->createValidLanguage();
        $language->setDateFormat($dateFormat);
        $violations = $this->validator->validate($language);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function dateFormatUncompatibleProvider(): array
    {
        return [
            ["« y-d-y »"],
            ["coucou²"],
            ["^ D €"],
            ["D d j l N S w z W F M m n t L o Y y"],
            ["m-d-y"],
            ["le N F Y"],
            [""],
            ["   "],
            ["dd-mm-yyyy"],
            ["dd/mm/Y"],
        ];
    }

    /**
     * @dataProvider timeFormatCompatibleProvider
     */
    public function testTimeFormatCompatible(string $dateFormat): void
    {
        $language = $this->createValidLanguage();
        $language->setTimeFormat($dateFormat);
        $violations = $this->validator->validate($language);
        $this->assertCount(0, $violations);
    }

    public function timeFormatCompatibleProvider(): array
    {
        return [
            ['H \h\e\u\r\e i:s T'],
            ['H \h i:s'],
            ['H:i:s O'],
            ['H:i:s T'],
            ['H:i:sP'],
            ['h:i'],
            ['\TH \h i:s \s\e\c T'],
            ['G:i a'],
            ['g:iA'],
            ['h:i:sA']
        ];
    }

    /**
     * @dataProvider timeFormatUncompatibleProvider
     * Liste des interdits : Symbole monétaire,
     * chiffre en exposant ou en indice, guillemet français
     * les caractères de contrôle invisibles et les points de code non utilisés
     */
    public function testTimeFormatUncompatible(string $dateFormat): void
    {
        $language = $this->createValidLanguage();
        $language->setTimeFormat($dateFormat);
        $violations = $this->validator->validate($language);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function timeFormatUncompatibleProvider(): array
    {
        return [
            ["« h-i-s »"],
            ["coucou²"],
            ["^ u €"],
            ["a A B g G h H i s u v e I O P p T Z"],
            ["H:i:s + O"],
            ["H:i:s .u"],
            ["H:i:s P"],
            ["H"],
            ["G"],
            ["GA"],
            [""],
            ["   "],
            ["hh:ii:ss"],
            ["hh-mm-ss"],
            ["ha"],
            ["g"]
        ];
    }

    /**
     * @dataProvider capitalTimeZoneUnconformityProvider
     */
    public function testCapitalTimeZoneUnconformity(string $timeZone): void
    {
        $language = $this->createValidLanguage();
        $language->setCapitalTimeZone($timeZone);
        $violations = $this->validator->validate($language);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function capitalTimeZoneUnconformityProvider(): array
    {
        return [
            ['Antartica/Inconnu'],
            ['Europe_Paris'],
            ['europe/Paris'],
            [''],
            ['   ']
        ];
    }

    /**
     * @dataProvider capitalTimeZoneConformityProvider
     */
    public function testCapitalTimeZoneConformity(string $timeZone): void
    {
        $language = $this->createValidLanguage();
        $language->setCapitalTimeZone($timeZone);
        $violations = $this->validator->validate($language);
        $this->assertCount(0, $violations);
    }

    public function capitalTimeZoneConformityProvider(): array
    {
        return [
            ['Antarctica/McMurdo'],
            ['Europe/Paris'],
            ['Africa/Johannesburg']
        ];
    }
}
