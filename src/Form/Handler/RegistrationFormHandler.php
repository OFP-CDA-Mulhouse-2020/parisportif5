<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\Language;
use App\Entity\User;
use App\Entity\Wallet;
use App\Security\EmailVerifier;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class RegistrationFormHandler
{
    private ?User $user = null;
    private string $countryCode = "FR";
    /** @var array<int,string> $languagesCodes */
    private array $languagesCodes = [];
    /** @const string DEFAULT_TIMEZONE */
    public const DEFAULT_TIMEZONE = 'Europe/Paris';

    /** @param array<int,string> $languagesCodes */
    public function __construct(array $languagesCodes = [])
    {
        $this->languagesCodes = $languagesCodes;
    }

    public function getUser(): User
    {
        if ($this->user === null) {
            $this->createUser();
        }
        return $this->user;
    }

    private function createUser(): void
    {
        $this->user = new User();
        $this->initializeUserTimeZone();
    }

    private function initializeUserTimeZone(): void
    {
        $preferredLanguageCode = $this->getICUPreferredLanguageCode() ?? $this->countryCode;
        $underscorePos = mb_stripos($preferredLanguageCode, '_');
        $this->countryCode = ($underscorePos !== false) ?
            mb_substr($preferredLanguageCode, $underscorePos + 1) : $preferredLanguageCode;
        $countryTimezones = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $this->countryCode);
        if (empty($countryTimezones)) {
            $countryTimezones[] = self::DEFAULT_TIMEZONE;
        }
        $this->user->setTimeZoneSelected($countryTimezones[0]);
    }

    private function getICUPreferredLanguageCode(): ?string
    {
        $icuPreferredLanguages = array_map(function (string $language) {
            if (mb_strlen($language) === 4) {
                return $language;
            }
        }, $this->languagesCodes);
        if (empty($icuPreferredLanguages)) {
            $icuPreferredLanguages[0] = $this->languagesCodes[0];
        }
        return $icuPreferredLanguages[0] ?? null;
    }

    public function handleAccountForm(
        FormInterface $form,
        Language $userLanguage,
        ObjectManager $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        EmailVerifier $emailVerifier
    ): void {
        // get data from form
        $this->user = $form->getData();
        // files directories
        $this->user->setIdentityDocument('filename.pdf');
        $this->user->setResidenceProof('filename.pdf');
        // encode the plain password
        $this->user->setPassword(
            $passwordEncoder->encodePassword(
                $this->user,
                $this->user->getPlainPassword()
            )
        );
        $this->user->eraseCredentials();
        // Set user roles
        $this->user->setRoles(['ROLE_USER']);
        // Set others user values
        $this->createUserWallet();
        $this->user->setLanguage($userLanguage);
        // Persist user
        $entityManager->persist($this->user);
        $entityManager->flush();
        // Verify user email
        //$this->verifyEmail($emailVerifier);
    }

    private function createUserWallet(): void
    {
        $userWallet = new Wallet();
        $userWallet
            ->setUser($this->user)
            ->setAmount(0);
        $this->user->setWallet($userWallet);
    }

    private function verifyEmail(EmailVerifier $emailVerifier): void
    {
        // generate a signed url and email it to the user
        $emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $this->user,
            (new TemplatedEmail())
                ->from(new Address('confirmation@bet-project.com', 'Confirmation Mail'))
                ->to((string) $this->user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
        // do anything else you need here, like send an email
    }
}
