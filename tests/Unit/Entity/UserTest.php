<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class UserTest extends WebTestCase
{
    //throw new \Exception($violations);
    private function timezoneInitialization(): \DateTimeZone
    {
        return new \DateTimeZone('Europe/Paris');
    }

    private function userInitialization(): User
    {
        $user = new User();
        $user
            ->setCivility("Monsieur")
            ->setFirstName("Dupont")
            ->setLastName("Tintin")
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

    private function kernelInitialization(): KernelInterface
    {
        $kernel = self::bootKernel();
        $kernel->boot();
        return $kernel;
    }

    /**
     * @dataProvider unexpectedCivilityProvider
     */
    public function testCivilityUnexpectedValue(string $civility): void
    {
        $kernel = $this->kernelInitialization();
        $user = $this->userInitialization();
        $user->setCivility($civility);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($user);
        $this->assertCount(1, $violations);
    }

    public function unexpectedCivilityProvider(): array
    {
        return [
            ["x"],
            ["mr"],
            [""]
        ];
    }

    public function testCivilityExpectedValue(): void
    {
        $civility1 = "Madame";
        $civility2 = "Monsieur";
        $kernel = $this->kernelInitialization();
        $user = $this->userInitialization();
        $user->setCivility($civility1);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($user);
        $this->assertCount(0, $violations);
        $user->setCivility($civility2);
        $violations = $validator->validate($user);
        $this->assertCount(0, $violations);
    }

    /**
     * @dataProvider unconformityEmailAddressProvider
     */
    public function testEmailAddressUnconformity(string $emailAddress): void
    {
        $kernel = $this->kernelInitialization();
        $user = $this->userInitialization();
        $user->setEmail($emailAddress);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($user);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function unconformityEmailAddressProvider(): array
    {
        return [
            ["emailtest.com"],
            ["test"],
            [""]
        ];
    }

    /**
     * @dataProvider conformityEmailAddressProvider
     */
    public function testEmailAddressConformity(string $emailAddress): void
    {
        $kernel = $this->kernelInitialization();
        $user = $this->userInitialization();
        $user->setEmail($emailAddress);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($user);
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
        $kernel = $this->kernelInitialization();
        $user = $this->userInitialization();
        $user->setFirstName($firstName);
        $user->setLastName($firstName);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($user);
        $this->assertGreaterThanOrEqual(2, count($violations));
    }

    public function nameUnconformityProvider(): array
    {
        return [
            ["gab#"],
            ["fa25"],
            ["monsieurdontlenomestbientroplong"],
            [""]
        ];
    }

    /**
     * @dataProvider nameConformityProvider
     */
    public function testNameConformity(string $firstName): void
    {
        $kernel = $this->kernelInitialization();
        $user = $this->userInitialization();
        $user->setFirstName($firstName);
        $user->setLastName($firstName);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($user);
        //throw new \Exception($violations);
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
        $kernel = $this->kernelInitialization();
        $user = $this->userInitialization();
        $user->setPassword($password);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($user);
        //throw new \Exception($violations);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function passwordUnconformityProvider(): array
    {
        return [
            [""],
            ["quedeslettresà"],
            ["61235478"]
        ];
    }

     /**
     * @dataProvider passwordConformityProvider
     */
    public function testPasswordConformity(string $password): void
    {
        $kernel = $this->kernelInitialization();
        $user = $this->userInitialization();
        $user->setPassword($password);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($user);
        //throw new \Exception($violations);
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
        $kernel = $this->kernelInitialization();
        $user = $this->userInitialization();
        $user->setBirthDate($birthDate);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($user);
        //throw new \Exception($violations);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function birthDateUnconformityProvider(): array
    {
        $timezone = $this->timezoneInitialization();
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
        $kernel = $this->kernelInitialization();
        $user = $this->userInitialization();
        $user->setBirthDate($birthDate);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($user);
        //throw new \Exception($violations);
        $this->assertCount(0, $violations);
    }

    public function birthDateConformityProvider(): array
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
    public function testTimeZoneSelectedUnconformity(string $timeZone): void
    {
        $kernel = $this->kernelInitialization();
        $user = $this->userInitialization();
        $user->setTimeZoneSelected($timeZone);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($user);
        //throw new \Exception($violations);
        $this->assertGreaterThanOrEqual(1, count($violations));
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
        $kernel = $this->kernelInitialization();
        $user = $this->userInitialization();
        $user->setTimeZoneSelected($timeZone);
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');
        $violations = $validator->validate($user);
        //throw new \Exception($violations);
        $this->assertCount(0, $violations);
    }

    public function timeZoneSelectedConformityProvider(): array
    {
        return [
            ['Antarctica/Troll'],
            ['Europe/Paris'],
            ['Africa/Johannesburg']
        ];
    }

    // public function testSuspendAccountWithoutActivation(): void
    // {
        /*$user = $this->userInitialization();
        $this->assertTrue($user->getActivatedStatus());
        $this->assertTrue($user->getSuspendedStatus());
        $user->valid();
        $this->assertFalse($user->getSuspendedStatus());
        $user->desactivate();
        $this->assertFalse($user->getActivatedStatus());
        $result1 = $user->suspend();
        $result2 = $user->suspend();
        $this->assertTrue($result1);
        $this->assertFalse($result2);
        $this->assertFalse($user->getSuspendedStatus());
        //$this->assertNotNull($user->getSuspendedDate());*/
    //}
}
