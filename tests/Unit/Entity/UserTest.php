<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Exception\AccountNotActiveException;
use App\Entity\Exception\BoundaryDateException;
use App\Entity\Exception\LegalAgeException;
use App\Entity\Exception\UnknownTimeZoneException;
use App\Entity\Exception\SpecialCharsException;
use App\Entity\Exception\FirstNameLengthException;
use App\Entity\Exception\LastNameLengthException;
use App\Entity\Exception\PasswordUppercaseException;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private function timezoneInitialization(): \DateTimeZone
    {
        return new \DateTimeZone('Europe/Paris');
    }

    private function userInitialization(): User
    {
        return new User();
    }

    public function testIsUserExistsWhithoutException(): void
    {
        $user = $this->userInitialization();
        $this->assertNotNull($user, "User n'est pas null");
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @dataProvider unexpectedCivilityProvider
     * @param mixed $civility
     */
    public function testCivilityUnexpectedValueException($civility): void
    {
        $user = $this->userInitialization();
        $this->expectException(\InvalidArgumentException::class);
        $user->setCivility($civility);
    }

    public function unexpectedCivilityProvider(): array
    {
        return [
            ["x"],
            ["monsieur"],
            [0]
        ];
    }

    public function testCivilityExpectedValue(): void
    {
        $civility1 = 'Madame';
        $civility2 = 'Monsieur';
        $this->assertIsString($civility1);
        $this->assertIsString($civility2);
        $user = $this->userInitialization();
        $user->setCivility($civility1);
        $this->assertSame($civility1, $user->getCivility());
        $user->setCivility($civility2);
        $this->assertSame($civility2, $user->getCivility());
    }

    /**
     * @dataProvider unconformityEmailAddressProvider
     * @param mixed $emailAddress
     */
    public function testEmailAddressUnconformityExeption($emailAddress): void
    {
        $user = $this->userInitialization();
        $this->expectException(\InvalidArgumentException::class);
        $user->setEmailAddress($emailAddress);
    }

    public function unconformityEmailAddressProvider(): array
    {
        return [
            ["emailtest.com"],
            ["test"],
            [0]
        ];
    }

    /**
     * @dataProvider conformityEmailAddressProvider
     */
    public function testEmailAddressConformity(string $emailAddress): void
    {
        $user = $this->userInitialization();
        //$this->assertIsString($emailAddress);
        $user->setEmailAddress($emailAddress);
        $this->assertSame($emailAddress, $user->getEmailAddress());
    }

    public function conformityEmailAddressProvider(): array
    {
        return [
            ["email@test.com"],
            ["test@orange.fr"],
            ["dupond2.dupont1@gmail.com"]
        ];
    }

    public function testFirstNameConformity(): void
    {
        $user = $this->userInitialization();
        $this->expectException(SpecialCharsException::class);
        $user->setFirstName("jojol@sticot");
    }

    /**
     * @dataProvider firstNameProvider
     */
    public function testFirstNameLength($fn): void
    {
        $user = $this->userInitialization();
        $this->expectException(FirstNameLengthException::class);
        $user->setFirstName($fn);
    }

    public function firstNameProvider(): array
    {
        return [
            [""],
            ["monsieurdontleprenomestbientroplong"]
        ];
    }

    public function testLastNameConformity(): void
    {
        $user = $this->userInitialization();
        $this->expectException(SpecialCharsException::class);
        $user->setLastName("jojol@sticot");
    }

    /**
     * @dataProvider lastNameProvider
     */
    public function testLastNameLength($ln): void
    {
        $user = $this->userInitialization();
        $this->expectException(LastNameLengthException::class);
        $user->setLastName($ln);
    }

    public function lastNameProvider(): array
    {
        return [
            [""],
            ["monsieurdontlenomdefamilleestbientroplong"]
        ];
    }

    /**
     * @dataProvider passwordProvider
     */
    public function testPasswordDoesNotContainUppercase($pw): void
    {
        $user = $this->userInitialization();
        $this->expectException(PasswordUppercaseException::class);
        $user->setPassword($pw);
    }

    public function passwordProvider(): array
    {
        return [
            [""],
            ["pasdemajuscules"],
            ["123547"]
            //["Pasdecaracterespeciaux123"],
            // ["Test1deP@sswordcorrect"]
        ];
    }

     /**
     * @dataProvider passwordUppercaseProvider
     */
    public function testPasswordContainsUppercase($pass): void
    {
        $user = $this->userInitialization();
        $user->setPassword($pass);
        $pw = $user->getPassword();
        $this->assertIsString($pass);
        //$this->assertContains('A', $pw);//assertRegexp
    }

    public function passwordUppercaseProvider(): array
    {
        return [
            ["AB123"],
            ["MajusculeOK"],
            ["123547P"]
            //["Pasdecaracterespeciaux123"],
            // ["Test1deP@sswordcorrect"]
        ];
    }

    /**
     * @dataProvider birthDateUnderLegalAgeProvider
     */
    public function testBirthDateUnderLegalAgeException(\DateTime $birthDate): void
    {
        $user = $this->userInitialization();
        //$this->assertInstanceOf(\DateTime::class, $birthDate);
        $this->expectException(LegalAgeException::class);
        $user->setBirthDate($birthDate);
    }

    public function birthDateUnderLegalAgeProvider(): array
    {
        $timezone = $this->timezoneInitialization();
        $legalAgeBirthDate = (new \DateTime('now', $timezone))->sub(new \DateInterval('P18Y'));
        return [
            [$legalAgeBirthDate->setTime(23, 59, 59, 999999)],
            [$legalAgeBirthDate->modify('+1 day')->setTime(23, 59, 59, 999999)]
        ];
    }

    /**
     * @dataProvider futurBirthDateBoundaryProvider
     */
    public function testFuturBirthDateBoundaryException(\DateTime $birthDate): void
    {
        $user = $this->userInitialization();
        //$this->assertInstanceOf(\DateTime::class, $birthDate);
        $this->expectException(BoundaryDateException::class);
        $user->setBirthDate($birthDate);
    }

    public function futurBirthDateBoundaryProvider(): array
    {
        $timezone = $this->timezoneInitialization();
        return [
            [(new \DateTime('now', $timezone))->setTime(23, 59, 60)],
            [(new \DateTime('now', $timezone))->add(new \DateInterval('P2Y'))]
        ];
    }

    /**
     * @dataProvider birthDateOverLegalAgeProvider
     */
    public function testBirthDateOverLegalAge(\DateTime $birthDate): void
    {
        $user = $this->userInitialization();
        //$this->assertInstanceOf(\DateTime::class, $birthDate);
        $user->setBirthDate($birthDate);
        $this->assertSame($birthDate, $user->getBirthDate());
    }

    public function birthDateOverLegalAgeProvider(): array
    {
        $timezone = $this->timezoneInitialization();
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
    public function testTimeZoneSelectedUnconformityException(string $timeZone): void
    {
        $user = $this->userInitialization();
        //$this->assertIsString($timeZone);
        $this->expectException(UnknownTimeZoneException::class);
        $user->setTimeZoneSelected($timeZone);
    }

    public function timeZoneSelectedUnconformityProvider(): array
    {
        return [
            ['Antartica/Inconnu'],
            ['Europe_Paris'],
            ['europe/Paris']
        ];
    }

    /**
     * @dataProvider timeZoneSelectedConformityProvider
     */
    public function testTimeZoneSelectedConformity(string $timeZone): void
    {
        $user = $this->userInitialization();
        //$this->assertIsString($timeZone);
        $user->setTimeZoneSelected($timeZone);
        $this->assertSame($timeZone, $user->getTimeZoneSelected());
    }

    public function timeZoneSelectedConformityProvider(): array
    {
        return [
            ['Antarctica/Troll'],
            ['Europe/Paris'],
            ['Africa/Johannesburg']
        ];
    }

    public function testSuspendAccountWithoutActivationException(): void
    {
        $user = $this->userInitialization();
        $this->expectException(AccountNotActiveException::class);
        $user->setActivatedStatus(false);
        $user->setSuspendedStatus(true);
    }

    public function testSuspendAccountWithActivation(): void
    {
        $user = $this->userInitialization();
        $user->setActivatedStatus(true);
        $user->setSuspendedStatus(true);
        $this->assertSame(true, $user->getSuspendedStatus());
    }

    /**
     * @dataProvider dateBoundaryUnconformityForAllStatusProvider
     */
    public function testDateBoundaryUnconformityForAllStatus(\DateTime $statusDate): void
    {
        $user = $this->userInitialization();
        $this->expectException(BoundaryDateException::class);
        $user->setActivatedStatus(true);
        $user->setActivatedDate($statusDate);
        $this->expectException(BoundaryDateException::class);
        $user->setSuspendedDate($statusDate);
        $this->expectException(BoundaryDateException::class);
        $user->setDeletedDate($statusDate);
    }

    public function dateBoundaryUnconformityForAllStatusProvider(): array
    {
        $timezone = $this->timezoneInitialization();
        return [
            [(new \DateTime('now', $timezone))->setTime(23, 59, 60)],
            [(new \DateTime('now', $timezone))->add(new \DateInterval('P2Y'))]
        ];
    }

    /**
     * @dataProvider dateConformityForAllStatusProvider
     */
    public function testDateConformityForAllStatus(\DateTime $statusDate): void
    {
        $user = $this->userInitialization();
        $user->setActivatedStatus(true);
        $user->setActivatedDate($statusDate);
        $this->assertSame($statusDate, $user->getActivatedDate());
        $user->setSuspendedDate($statusDate);
        $this->assertSame($statusDate, $user->getSuspendedDate());
        $user->setDeletedDate($statusDate);
        $this->assertSame($statusDate, $user->getDeletedDate());
    }

    public function dateConformityForAllStatusProvider(): array
    {
        $timezone = $this->timezoneInitialization();
        return [
            [(new \DateTime('now', $timezone))->setTime(0, 0)],
            [(new \DateTime('now', $timezone))->sub(new \DateInterval('P1D'))]
        ];
    }
}
