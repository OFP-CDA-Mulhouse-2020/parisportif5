<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\User;
use App\Entity\Wallet;
use App\Entity\Language;
use App\Service\FileUploader;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Address;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    /** @param string[] $filesDirectory */
    public function handleForm(
        FormInterface $form,
        Language $userLanguage,
        ObjectManager $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        EmailVerifier $emailVerifier,
        FileUploader $fileUploader,
        array $filesDirectory
    ): void {
        // get data from form
        $this->user = $form->getData();
        // files
        $identityDocument = $form->get("identityDocument")->getData();
        $identityDocumentDirectory = $filesDirectory["identity_directory"];
        $fileUploader->setTargetDirectory($identityDocumentDirectory);
        $identityDocumentFileName = $fileUploader->upload($identityDocument);
        $this->user->setIdentityDocument($identityDocumentFileName);
        $residenceProof = $form->get("residenceProof")->getData();
        $residenceProofDirectory = $filesDirectory["residence_directory"];
        $fileUploader->setTargetDirectory($residenceProofDirectory);
        $residenceProofFileName = $fileUploader->upload($residenceProof);
        $this->user->setResidenceProof($residenceProofFileName);
        // encode the plain password
        $this->user->setPassword(
            $passwordEncoder->encodePassword(
                $this->user,
                $this->user->getPlainPassword()
            )
        );
        // delete the plain password
        $this->user->eraseCredentials();
        // Set user roles
        $this->user->setRoles(['ROLE_USER']);
        // Set others user values
        $this->createUserWallet();
        $this->user->setLanguage($userLanguage);
        // Verify user email
        //$this->verifyEmail($emailVerifier);
        // Persist user
        $entityManager->persist($this->user);
        $entityManager->flush();
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
            'account_verify_email',
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
