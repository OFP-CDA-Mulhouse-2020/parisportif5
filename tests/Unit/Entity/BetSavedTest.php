<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\BetSaved;
use App\Service\DateTimeStorageDataConverter;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \BetSaved
 */
final class BetSavedTest extends WebTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createValidBetSaved(): BetSaved
    {
        $converter = new DateTimeStorageDataConverter();
        $betSaved = new BetSaved($converter);
        $date = new \DateTimeImmutable("now", new \DateTimeZone("UTC"));
        $date = $date->modify("-1 day");
        $betSaved
            ->setDateTimeConverter($converter)
            ->setDesignation('paris')
            ->setAmount(100)
            ->setOdds('1.2')
            ->setBetDate($date)
            ->setGains(0)
            ->setBetCategoryName('result')
            ->setCompetitionName('championnat')
            ->setCompetitionCountry('FR')
            ->setCompetitionStartDate($date)
            ->setCompetitionSportName('foot')
            ->setCompetitionSportCountry('FR');
            /*->setRunEvent('')
            ->setRunName('')
            ->setRunStartDate('')
            ->setTeamName('')
            ->setTeamCountry('')
            ->setMemberLastName('')
            ->setMemberFirstName('')
            ->setMemberCountry('');*/
        return $betSaved;
    }

    private function createDefaultTimeZone(): \DateTimeZone
    {
        return new \DateTimeZone('UTC');
    }

    /**
     * @dataProvider designationCompatibleProvider
     */
    public function testDesignationCompatible(string $designation): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setDesignation($designation);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(0, $violations);
    }

    public function designationCompatibleProvider(): array
    {
        return [
            ["paris sur le match PSG contre Truc, machin vainqueur"],
            ["PSG 1 <()[{]}>=+-*/\_?!;,:"]
        ];
    }

    public function testDesignationUncompatible(): void
    {
        $designation1 = '';
        $designation2 = '   ';
        $betSaved = $this->createValidBetSaved();
        $betSaved->setDesignation($designation1);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(1, $violations);
        $betSaved->setDesignation($designation2);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(1, $violations);
    }

    public function testAmountCompatible(): void
    {
        $amount1 = 0;
        $amount2 = 1000000000;
        $betSaved = $this->createValidBetSaved();
        $betSaved->setAmount($amount1);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(0, $violations);
        $betSaved->setAmount($amount2);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(0, $violations);
    }

    public function testAmountUncompatible(): void
    {
        $amount = -1;
        $betSaved = $this->createValidBetSaved();
        $betSaved->setAmount($amount);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(1, $violations);
    }

    public function testOddsCompatible(): void
    {
        $odds1 = '0.00';
        $odds2 = '99999999.99';
        $betSaved = $this->createValidBetSaved();
        $betSaved->setOdds($odds1);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(0, $violations);
        $betSaved->setOdds($odds2);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider oddsUncompatibleProvider
     */
    public function testOddsUncompatible(string $odds): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setOdds($odds);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(1, $violations);
    }

    public function oddsUncompatibleProvider(): array
    {
        return [
            ['-1'],
            ['100000000'],
            ['string']
        ];
    }

    /**
     * @dataProvider betCategoryNamePropertyCompatibleProvider
     */
    public function testBetCategoryNamePropertyCompatible(string $betCategoryName): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setBetCategoryName($betCategoryName);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(0, $violations);
    }

    public function betCategoryNamePropertyCompatibleProvider(): array
    {
        return [
            ["resultw"],
            ["result-and-points"],
            ["result_points"]
        ];
    }

    /**
     * @dataProvider betCategoryNamePropertyUncompatibleProvider
     */
    public function testBetCategoryNamePropertyUncompatible(string $betCategoryName): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setBetCategoryName($betCategoryName);
        $violations = $this->validator->validate($betSaved);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function betCategoryNamePropertyUncompatibleProvider(): array
    {
        return [
            ["result player"],
            ["resultbenched_"],
            ["-result"],
            ["poïnts"],
            [''],
            ['  ']
        ];
    }

        /**
     * @dataProvider competitionNamePropertyCompatibleProvider
     */
    public function testCompetitionNamePropertyCompatible(string $competitionName): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setCompetitionName($competitionName);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(0, $violations);
    }

    public function competitionNamePropertyCompatibleProvider(): array
    {
        return [
            ["Grand prix de France"],
            ["Championnat de France"]
        ];
    }

    public function testCompetitionNamePropertyUncompatible(): void
    {
        $competitionName1 = '';
        $competitionName2 = '   ';
        $betSaved = $this->createValidBetSaved();
        $betSaved->setCompetitionName($competitionName1);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(1, $violations);
        $betSaved->setCompetitionName($competitionName2);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(1, $violations);
    }

    public function testIfCompetitionSportNameIsNotEmpty(): void
    {
        $competitionSportName = "foot";
        $betSaved = $this->createValidBetSaved();
        $betSaved->setCompetitionSportName($competitionSportName);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(0, $violations);
    }

    public function testIfCompetitionSportNameIsEmpty(): void
    {
        $competitionSportName = "";
        $betSaved = $this->createValidBetSaved();
        $betSaved->setCompetitionSportName($competitionSportName);
        $violations = $this->validator->validate($betSaved);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    /**
     * @dataProvider runNamePropertyCompatibleProvider
     */
    public function testRunNamePropertyCompatible(string $runName): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setRunName($runName);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(0, $violations);
    }

    public function runNamePropertyCompatibleProvider(): array
    {
        return [
            ["Spécial n°1 des Vosges"],
            ["Match pool n°1 France-Espagne"]
        ];
    }

    public function testRunNamePropertyUncompatible(): void
    {
        $runName1 = '';
        $runName2 = '   ';
        $betSaved = $this->createValidBetSaved();
        $betSaved->setRunName($runName1);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(1, $violations);
        $betSaved->setRunName($runName2);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(1, $violations);
    }

    /**
     * @dataProvider runEventPropertyCompatibleProvider
     */
    public function testRunEventPropertyCompatible(string $runEvent): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setRunEvent($runEvent);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(0, $violations);
    }

    public function runEventPropertyCompatibleProvider(): array
    {
        return [
            ["Championnat des Vosges"],
            ["Matchs de pool n°1"]
        ];
    }

    public function testRunEventPropertyUncompatible(): void
    {
        $runEvent1 = '';
        $runEvent2 = '   ';
        $betSaved = $this->createValidBetSaved();
        $betSaved->setRunEvent($runEvent1);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(1, $violations);
        $betSaved->setRunEvent($runEvent2);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(1, $violations);
    }

        /**
     * @dataProvider validTeamNameProvider
     */
    public function testIfTeamNameIsValid(string $teamName): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setTeamName($teamName);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(0, $violations);
    }

    public function validTeamNameProvider(): array
    {
        return [
            ["Paris Saint-Germain Football Club"],
            ["Västerås Hockey"],
            ["Fenerbahçe 1907"],
            ["Székesfehérvár Futball"],
            ["Cartagena 1444"],
            ["A.E.K"],
            ["OLA"],
            ["OL"]
        ];
    }

    /**
     * @dataProvider invalidTeamNameProvider
     */
    public function testIfTeamNameIsInvalid(string $teamName): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setTeamName($teamName);
        $violations = $this->validator->validate($betSaved);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidTeamNameProvider(): array
    {
        return [
            ["W@tkins Glen"],
            ["/\/ercedes"],
            ["Mon€yTeam"],
            ["_"],
            ["..."],
            ["---"],
            ["'''"],
            ["nom   d'equipe"],
            ["A"],
            [""]
        ];
    }

    /**
     * @dataProvider validMemberLastNameProvider
     */
    public function testIfMemberLastNameIsValid(string $memberLastName): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setMemberLastName($memberLastName);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(0, $violations);
    }

    public function validMemberLastNameProvider(): array
    {
        return [
            ["Van Der Weg"],
            ["Höhenhausen"],
            ["Gonzalo-Viñales"],
            ["Åaland"],
            ["Sjålle"],
            ["Lindstrøm"],
            ["Üçkup"]
        ];
    }

    /**
     * @dataProvider invalidMemberLastNameProvider
     */
    public function testIfMemberLastNameIsInvalid(string $memberLastName): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setMemberLastName($memberLastName);
        $violations = $this->validator->validate($betSaved);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidMemberLastNameProvider(): array
    {
        return [
            ["SPARRO\/\/"],
            ["H@land"],
            ["2"]
        ];
    }

    /**
     * @dataProvider validMemberFirstNameProvider
     */
    public function testIfMemberFirstNameIsValid(string $memberFirstName): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setMemberFirstName($memberFirstName);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(0, $violations);
    }

    public function validMemberFirstNameProvider(): array
    {
        return [
            ["Antoinette"],
            ["Höx"],
            ["Nuño"],
            ["Åssel"],
            ["Sjåndra"],
            ["Pierre-Anthoine"],
            ["Joël"]
        ];
    }

    /**
     * @dataProvider invalidMemberFirstNameProvider
     */
    public function testIfMemberFirstNameIsInvalid(string $memberFirstName): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setMemberFirstName($memberFirstName);
        $violations = $this->validator->validate($betSaved);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function invalidMemberFirstNameProvider(): array
    {
        return [
            ["\/\/ils0n"],
            ["H@rry"],
            ["Moumoute2"],
            ["♥"],
            [""]
        ];
    }

    /**
     * @dataProvider startDateUnconformityProvider
     */
    public function testStartDateUnconformity(\DateTimeInterface $startDate): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setCompetitionStartDate($startDate);
        $betSaved->setRunStartDate($startDate);
        $violations = $this->validator->validate($betSaved);
        $this->assertGreaterThanOrEqual(2, count($violations));
    }

    public function startDateUnconformityProvider(): array
    {
        $timezone = $this->createDefaultTimeZone();
        $startDate = new \DateTimeImmutable('now', $timezone);
        return [
            [$startDate->modify("+1 day")],
            [$startDate->modify("+1 month")]
        ];
    }

    /**
     * @dataProvider startDateConformityProvider
     */
    public function testStartDateConformity(\DateTimeInterface $startDate): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setCompetitionStartDate($startDate);
        $betSaved->setRunStartDate($startDate);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(0, $violations);
    }

    public function startDateConformityProvider(): array
    {
        $timezone = $this->createDefaultTimeZone();
        $startDate = new \DateTimeImmutable('now', $timezone);
        return [
            [$startDate],
            [$startDate->modify('-1 hour')],
            [$startDate->modify('-1 day')->setTime(23, 59, 59, 999999)],
            [$startDate->modify('-1 year')]
        ];
    }

    /**
     * @dataProvider countryCompatibleProvider
     * ISO 3166-1 alpha-2 => 2 lettres majuscules
     */
    public function testCountryCompatible(string $country): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setCompetitionSportCountry($country);
        $betSaved->setCompetitionCountry($country);
        $betSaved->setTeamCountry($country);
        $betSaved->setMemberCountry($country);
        $violations = $this->validator->validate($betSaved);
        $this->assertCount(0, $violations);
    }

    public function countryCompatibleProvider(): array
    {
        return [
            ["FR"],
            ["DE"]
        ];
    }

    /**
     * @dataProvider countryUncompatibleProvider
     */
    public function testCountryUncompatible(string $country): void
    {
        $betSaved = $this->createValidBetSaved();
        $betSaved->setCompetitionSportCountry($country);
        $betSaved->setCompetitionCountry($country);
        $betSaved->setTeamCountry($country);
        $betSaved->setMemberCountry($country);
        $violations = $this->validator->validate($betSaved);
        $this->assertGreaterThanOrEqual(4, count($violations));
    }

    public function countryUncompatibleProvider(): array
    {
        return [
            ["XY"],
            ["FRA"],
            ["France"],
            ["fr"],
            [''],
            ['   ']
        ];
    }
}
