<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    //throw new \Exception($violations);
    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function createDefaultTimeZone(): \DateTimeZone
    {
        return new \DateTimeZone('UTC');
    }

    private function createValidUser(): User
    {
        $user = new User();
        $user
            ->setCivility("Monsieur")
            ->setFirstName("Tintin")
            ->setLastName("Dupont")
            ->setBillingAddress("1 avenue st martin")
            ->setBillingCity("Colmar")
            ->setBillingPostcode("68000")
            ->setBillingCountry("FR")
            ->setBirthDate(new \DateTime("2000-10-10"))
            ->setPassword("Azerty78")
            ->setEmail("dupond.t@orange.fr")
            ->setTimeZoneSelected("Europe/Paris");
        return $user;
    }

    /**
     * @dataProvider civilityConformityProvider
     */
    public function testCivilityConformity(string $civility): void
    {
        $user = $this->createValidUser();
        $user->setCivility($civility);
        $violations = $this->validator->validate($user);
        $this->assertCount(0, $violations);
    }

    public function civilityConformityProvider(): array
    {
        return [
            ["x"],
            ["mr"],
            ["Madame"],
            ["Monsieur"]
        ];
    }

    public function testCivilityUnconformity(): void
    {
        $civility1 = "";
        $civility2 = "    ";
        $civility3 = "beaucouptroplonguecivilité";
        $user = $this->createValidUser();
        $user->setCivility($civility1);
        $violations = $this->validator->validate($user);
        $this->assertCount(1, $violations);
        $user->setCivility($civility2);
        $violations = $this->validator->validate($user);
        $this->assertCount(1, $violations);
        $user->setCivility($civility3);
        $violations = $this->validator->validate($user);
        $this->assertCount(1, $violations);
    }

    /**
     * @dataProvider unconformityEmailAddressProvider
     */
    public function testEmailAddressUnconformity(string $emailAddress): void
    {
        $user = $this->createValidUser();
        $user->setEmail($emailAddress);
        $violations = $this->validator->validate($user);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function unconformityEmailAddressProvider(): array
    {
        return [
            ["emailtest.com"],
            ["test"],
            ["   "],
            [""]
        ];
    }

    /**
     * @dataProvider conformityEmailAddressProvider
     */
    public function testEmailAddressConformity(string $emailAddress): void
    {
        $user = $this->createValidUser();
        $user->setEmail($emailAddress);
        $violations = $this->validator->validate($user);
        $this->assertCount(0, $violations);
    }

    public function conformityEmailAddressProvider(): array
    {
        return [
            ["email@test.com"],
            ["test@orange.fr"],
            ["dupond2.dupont1@gmail.com"]
        ];
    }

    /**
     * @dataProvider nameUnconformityProvider
     */
    public function testNameUnconformity(string $firstName): void
    {
        $user = $this->createValidUser();
        $user->setFirstName($firstName);
        $user->setLastName($firstName);
        $violations = $this->validator->validate($user);
        $this->assertGreaterThanOrEqual(2, count($violations));
    }

    public function nameUnconformityProvider(): array
    {
        return [
            ["gab#"],
            ["fa25"],
            ["monsieurdontlenomestbientroplong"],
            [""],
            ["   "]
        ];
    }

    /**
     * @dataProvider nameConformityProvider
     */
    public function testNameConformity(string $firstName): void
    {
        $user = $this->createValidUser();
        $user->setFirstName($firstName);
        $user->setLastName($firstName);
        $violations = $this->validator->validate($user);
        $this->assertCount(0, $violations);
    }

    public function nameConformityProvider(): array
    {
        return [
            ["Anaïs"],
            ["édouârd"],
            ["Jean-Marc de l'Atour"],
            ["ggg"]
        ];
    }

    /**
     * @dataProvider passwordUnconformityProvider
     */
    public function testPasswordUnconformity(string $password): void
    {
        $user = $this->createValidUser();
        $user->setPassword($password);
        $violations = $this->validator->validate($user);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function passwordUnconformityProvider(): array
    {
        return [
            [""],
            ["  "],
            ["quedeslettresà"],
            ["61235478"]
        ];
    }

     /**
     * @dataProvider passwordConformityProvider
     */
    public function testPasswordConformity(string $password): void
    {
        $user = $this->createValidUser();
        $user->setPassword($password);
        $violations = $this->validator->validate($user);
        $this->assertCount(0, $violations);
    }

    public function passwordConformityProvider(): array
    {
        return [
            ["P123547"],
            ["&123547"],
            ["aveccaracterespeciaux123"],
            ["Aveccaracterespeciaux#"],
            ["Test1deP@sswordcorrect"]
        ];
    }

    /**
     * @dataProvider birthDateUnconformityProvider
     */
    public function testBirthDateUnconformity(\DateTime $birthDate): void
    {
        $user = $this->createValidUser();
        $user->setBirthDate($birthDate);
        $violations = $this->validator->validate($user);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function birthDateUnconformityProvider(): array
    {
        $timezone = $this->createDefaultTimeZone();
        $legalAgeBirthDate = (new \DateTime('now', $timezone))->sub(new \DateInterval('P18Y'));
        return [
            [$legalAgeBirthDate->setTime(23, 59, 59, 999999)],
            [$legalAgeBirthDate->modify('+1 day')->setTime(23, 59, 59, 999999)],
            [(new \DateTime('now', $timezone))->setTime(23, 59, 60)],
            [(new \DateTime('now', $timezone))->add(new \DateInterval('P2Y'))]
        ];
    }

    /**
     * @dataProvider birthDateConformityProvider
     */
    public function testBirthDateConformity(\DateTime $birthDate): void
    {
        $user = $this->createValidUser();
        $user->setBirthDate($birthDate);
        $violations = $this->validator->validate($user);
        $this->assertCount(0, $violations);
    }

    public function birthDateConformityProvider(): array
    {
        $timezone = $this->createDefaultTimeZone();
        $legalAgeBirthDate1 = (new \DateTime('now', $timezone))->sub(new \DateInterval('P18Y'));
        $legalAgeBirthDate2 = clone $legalAgeBirthDate1;
        return [
            [$legalAgeBirthDate1->modify("-1 day")],
            [$legalAgeBirthDate2->modify("-1 year")]
        ];
    }

    /**
     * @dataProvider timeZoneSelectedUnconformityProvider
     */
    public function testTimeZoneSelectedUnconformity(string $timeZone): void
    {
        $user = $this->createValidUser();
        $user->setTimeZoneSelected($timeZone);
        $violations = $this->validator->validate($user);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function timeZoneSelectedUnconformityProvider(): array
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
     * @dataProvider timeZoneSelectedConformityProvider
     */
    public function testTimeZoneSelectedConformity(string $timeZone): void
    {
        $user = $this->createValidUser();
        $user->setTimeZoneSelected($timeZone);
        $violations = $this->validator->validate($user);
        $this->assertCount(0, $violations);
    }

    public function timeZoneSelectedConformityProvider(): array
    {
        return [
            ['Antarctica/McMurdo'],
            ['Europe/Paris'],
            ['Africa/Johannesburg']
        ];
    }

    /**
     * @dataProvider billingAddressCompatibleProvider
     */
    public function testBillingAddressCompatible(string $address)
    {
        $user = $this->createValidUser();
        $user->setBillingAddress($address);
        $violations = $this->validator->validate($user);
        $this->assertCount(0, $violations);
    }

    public function billingAddressCompatibleProvider(): array
    {
        return [
            ["5, rue Jean-Gabin"],
            ["lieu-dit LeFabien"],
            ["Rue de l'Abbaye"],
            ["ggg"]
        ];
    }

    /**
     * @dataProvider billingAddressUncompatibleProvider
     */
    public function testBillingAddressUncompatible(string $address)
    {
        $user = $this->createValidUser();
        $user->setBillingAddress($address);
        $violations = $this->validator->validate($user);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function billingAddressUncompatibleProvider(): array
    {
        return [
            ["5, rue Jean-Gabin#"],
            ["lieu-dit LeFabien@"],
            ["Rue de l`Abbaye"],
            [""],
            ["    "]
        ];
    }

      /**
     * @dataProvider billingCityCompatibleProvider
     */
    public function testBillingCityCompatible(string $city)
    {
        $user = $this->createValidUser();
        $user->setBillingCity($city);
        $violations = $this->validator->validate($user);
        $this->assertCount(0, $violations);
    }

    public function billingCityCompatibleProvider(): array
    {
        return [
            ["Saint-Jean de L'Arche"],
            ["Paris"],
            ["londre"]
        ];
    }

    /**
     * @dataProvider billingCityUncompatibleProvider
     */
    public function testBillingCityUncompatible(string $city)
    {
        $user = $this->createValidUser();
        $user->setBillingCity($city);
        $violations = $this->validator->validate($user);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function billingCityUncompatibleProvider(): array
    {
        return [
            ["1the village"],
            ["P@ris"],
            ["londre,"],
            [''],
            ['  ']
        ];
    }

    /**
     * @dataProvider billingPostcodeCompatibleProvider
     */
    public function testBillingPostcodeCompatible(string $postcode)
    {
        $user = $this->createValidUser();
        $user->setBillingPostcode($postcode);
        $violations = $this->validator->validate($user);
        $this->assertCount(0, $violations);
    }

    public function billingPostcodeCompatibleProvider(): array
    {
        return [
            ["68000"],
            ["CP-Index 7000"]
        ];
    }

    /**
     * @dataProvider billingPostcodeUncompatibleProvider
     */
    public function testBillingPostcodeUncompatible(string $postcode)
    {
        $user = $this->createValidUser();
        $user->setBillingPostcode($postcode);
        $violations = $this->validator->validate($user);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function billingPostcodeUncompatibleProvider(): array
    {
        return [
            ["68000@"],
            ["CP'Index 7000"],
            [''],
            ['  ']
        ];
    }

    /**
     * @dataProvider billingCountryCompatibleProvider
     * ISO 3166-1 alpha-2 => 2 lettres majuscules
     */
    public function testBillingCountryCompatible(string $country)
    {
        $user = $this->createValidUser();
        $user->setBillingCountry($country);
        $violations = $this->validator->validate($user);
        $this->assertCount(0, $violations);
    }

    public function billingCountryCompatibleProvider(): array
    {
        return [
            ["FR"],
            ["DE"]
        ];
    }

    /**
     * @dataProvider billingCountryUncompatibleProvider
     */
    public function testBillingCountryUncompatible(string $country)
    {
        $user = $this->createValidUser();
        $user->setBillingCountry($country);
        $violations = $this->validator->validate($user);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function billingCountryUncompatibleProvider(): array
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

    public function testValidAccountWithoutActivation(): void
    {
        $user = $this->createValidUser();
        $this->assertTrue($user->getActivatedStatus());
        $this->assertTrue($user->getSuspendedStatus());
        $this->assertNotNull($user->getSuspendedDate());
        $user->desactivate();
        $this->assertFalse($user->getActivatedStatus());
        $result1 = $user->valid();
        $result2 = $user->valid();
        $this->assertFalse($result1);
        $this->assertFalse($result2);
        $this->assertTrue($user->getSuspendedStatus());
        $this->assertNotNull($user->getSuspendedDate());
    }

    public function testValidAccountWithActivation(): void
    {
        $user = $this->createValidUser();
        $this->assertTrue($user->getActivatedStatus());
        $this->assertTrue($user->getSuspendedStatus());
        $this->assertNotNull($user->getSuspendedDate());
        $result1 = $user->valid();
        $result2 = $user->valid();
        $this->assertTrue($result1);
        $this->assertFalse($result2);
        $this->assertFalse($user->getSuspendedStatus());
        $this->assertNull($user->getSuspendedDate());
    }

    public function testSuspendAccountWithoutActivation(): void
    {
        $user = $this->createValidUser();
        $this->assertTrue($user->getActivatedStatus());
        $this->assertTrue($user->getSuspendedStatus());
        $this->assertNotNull($user->getSuspendedDate());
        $user->valid();
        $this->assertFalse($user->getSuspendedStatus());
        $user->desactivate();
        $this->assertFalse($user->getActivatedStatus());
        $result1 = $user->suspend();
        $result2 = $user->suspend();
        $this->assertFalse($result1);
        $this->assertFalse($result2);
        $this->assertFalse($user->getSuspendedStatus());
        $this->assertNull($user->getSuspendedDate());
    }

    public function testSuspendAccountWithActivation(): void
    {
        $user = $this->createValidUser();
        $this->assertTrue($user->getActivatedStatus());
        $this->assertTrue($user->getSuspendedStatus());
        $this->assertNotNull($user->getSuspendedDate());
        $user->valid();
        $this->assertFalse($user->getSuspendedStatus());
        $result1 = $user->suspend();
        $result2 = $user->suspend();
        $this->assertTrue($result1);
        $this->assertFalse($result2);
        $this->assertTrue($user->getSuspendedStatus());
        $this->assertNotNull($user->getSuspendedDate());
    }

    public function testActivateAccount(): void
    {
        $user = $this->createValidUser();
        $this->assertTrue($user->getActivatedStatus());
        $this->assertNotNull($user->getActivatedDate());
        $user->desactivate();
        $this->assertFalse($user->getActivatedStatus());
        $this->assertNull($user->getActivatedDate());
        $result1 = $user->activate();
        $result2 = $user->activate();
        $this->assertTrue($result1);
        $this->assertFalse($result2);
        $this->assertTrue($user->getActivatedStatus());
        $this->assertNotNull($user->getActivatedDate());
    }

    public function testDesactivateAccount(): void
    {
        $user = $this->createValidUser();
        $this->assertTrue($user->getActivatedStatus());
        $result1 = $user->desactivate();
        $result2 = $user->desactivate();
        $this->assertTrue($result1);
        $this->assertFalse($result2);
        $this->assertFalse($user->getActivatedStatus());
        $this->assertNull($user->getActivatedDate());
    }

    public function testDeleteAccount(): void
    {
        $user = $this->createValidUser();
        $this->assertFalse($user->getDeletedStatus());
        $this->assertNull($user->getDeletedDate());
        $result1 = $user->delete();
        $result2 = $user->delete();
        $this->assertTrue($result1);
        $this->assertFalse($result2);
        $this->assertTrue($user->getDeletedStatus());
        $this->assertNotNull($user->getDeletedDate());
    }

    public function testRestoreAccount(): void
    {
        $user = $this->createValidUser();
        $this->assertFalse($user->getDeletedStatus());
        $this->assertNull($user->getDeletedDate());
        $user->delete();
        $this->assertTrue($user->getDeletedStatus());
        $this->assertNotNull($user->getDeletedDate());
        $result1 = $user->restore();
        $result2 = $user->restore();
        $this->assertTrue($result1);
        $this->assertFalse($result2);
        $this->assertFalse($user->getDeletedStatus());
        $this->assertNull($user->getDeletedDate());
    }

    public function testIsTruePasswordSafe(): void
    {
        $user = $this->createValidUser();
        $result = $user->isPasswordSafe();
        $this->assertTrue($result);
        $violations = $this->validator->validate($user);
        $this->assertCount(0, $violations);
    }

    public function testIsFalsePasswordSafe(): void
    {
        $user = $this->createValidUser();
        $user->setPassword("tintin45335");
        $result = $user->isPasswordSafe();
        $this->assertFalse($result);
        $violations = $this->validator->validate($user);
        $this->assertCount(1, $violations);
    }

    public function testMethodGetFullName(): void
    {
        $user = $this->createValidUser();
        $result = method_exists($user, 'getFullName');
        $this->assertTrue($result);
        $result = ($user->getFullName() ?? '');
        $this->assertStringContainsString(($user->getFirstName() ?? ''), $result);
        $this->assertStringContainsString(($user->getLastName() ?? ''), $result);
    }

    public function testMethodGetFullAddress(): void
    {
        $user = $this->createValidUser();
        $result = method_exists($user, 'getFullAddress');
        $this->assertTrue($result);
        $result = ($user->getFullAddress() ?? '');
        $this->assertStringContainsString(($user->getBillingAddress() ?? ''), $result);
        $this->assertStringContainsString(($user->getBillingCity() ?? ''), $result);
        $this->assertStringContainsString(($user->getBillingPostcode() ?? ''), $result);
        $this->assertStringContainsString(($user->getBillingCountry() ?? ''), $result);
    }

    public function testConstantMinAgeForBetting(): void
    {
        $user = $this->createValidUser();
        $className = get_class($user);
        $result = defined($className . '::MIN_AGE_FOR_BETTING');
        $this->assertTrue($result);
        $this->assertSame(18, $user::MIN_AGE_FOR_BETTING);
    }

    public function testConstantDatabaseTimeZone(): void
    {
        $user = $this->createValidUser();
        $className = get_class($user);
        $result = defined($className . '::STORED_TIME_ZONE');
        $this->assertTrue($result);
        $this->assertSame('UTC', $user::STORED_TIME_ZONE);
    }

    public function testConstantSelectCurrencyName(): void
    {
        $user = $this->createValidUser();
        $className = get_class($user);
        $result = defined($className . '::SELECT_CURRENCY_NAME');
        $this->assertTrue($result);
        $this->assertSame('Euro', $user::SELECT_CURRENCY_NAME);
    }

    public function testConstantSelectCurrencySymbol(): void
    {
        $user = $this->createValidUser();
        $className = get_class($user);
        $result = defined($className . '::SELECT_CURRENCY_SYMBOL');
        $this->assertTrue($result);
        $this->assertSame('€', $user::SELECT_CURRENCY_SYMBOL);
    }
}
