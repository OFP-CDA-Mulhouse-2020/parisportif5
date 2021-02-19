<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\User;
use App\Entity\Wallet;
use App\Entity\Language;
use App\Service\FileUploader;
use App\Security\EmailVerifier;
use App\Form\Model\UserFormModel;
use Symfony\Component\Mime\Address;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class RegistrationFormHandler
{
    private string $countryCode = "FR";
    /** @const string DEFAULT_TIMEZONE */
    public const DEFAULT_TIMEZONE = 'Europe/Paris';

    /** @param array<int,string> $languagesCodes */
    public function initializeUserFormModel(UserFormModel $userFormModel, array $languagesCodes): UserFormModel
    {
        if (!empty($languagesCodes) === true) {
            $timeZoneSelected = $this->getUserTimeZone($languagesCodes);
            $userFormModel->setTimeZoneSelected($timeZoneSelected);
        }
        return $userFormModel;
    }

    /** @param array<int,string> $languagesCodes */
    private function getUserTimeZone(array $languagesCodes): string
    {
        $preferredLanguageCode = $this->getICUPreferredLanguageCode($languagesCodes) ?? $this->countryCode;
        $underscorePos = mb_stripos($preferredLanguageCode, '_');
        $this->countryCode = ($underscorePos !== false) ?
            mb_substr($preferredLanguageCode, $underscorePos + 1) : $preferredLanguageCode;
        $countryTimezones = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $this->countryCode);
        if (empty($countryTimezones)) {
            $countryTimezones[] = self::DEFAULT_TIMEZONE;
        }
        return $countryTimezones[0];
    }

    /** @param array<int,string> $languagesCodes */
    private function getICUPreferredLanguageCode(array $languagesCodes): ?string
    {
        $icuPreferredLanguages = array_map(function (string $language) {
            if (mb_strlen($language) === 4) {
                return $language;
            }
        }, $languagesCodes);
        if (empty($icuPreferredLanguages)) {
            $icuPreferredLanguages[0] = $languagesCodes[0];
        }
        return $icuPreferredLanguages[0] ?? null;
    }

    private function initializeUser(
        User $user,
        UserFormModel $userFormModel
    ): User {
        $user->setEmail($userFormModel->getEmail());
        $user->setCivility($userFormModel->getCivility());
        $user->setFirstName($userFormModel->getFirstName());
        $user->setLastName($userFormModel->getLastName());
        $user->setBillingAddress($userFormModel->getBillingAddress());
        $user->setBillingCity($userFormModel->getBillingCity());
        $user->setBillingPostcode($userFormModel->getBillingPostcode());
        $user->setBillingCountry($userFormModel->getBillingCountry());
        $user->setBirthDate($userFormModel->getBirthDate());
        $user->setTimeZoneSelected($userFormModel->getTimeZoneSelected());
        $user->setNewsletters($userFormModel->getNewsletters());
        return $user;
    }

    /** @param string[] $filesDirectory */
    private function saveFiles(
        User $user,
        FileUploader $fileUploader,
        UserFormModel $userFormModel,
        array $filesDirectory
    ): User {
        $identityDocument = $userFormModel->getIdentityDocument();
        $identityDocumentDirectory = $filesDirectory["identity_directory"];
        $fileUploader->setTargetDirectory($identityDocumentDirectory);
        $identityDocumentFileName = $fileUploader->upload($identityDocument);
        $user->setIdentityDocument($identityDocumentFileName);
        $residenceProof = $userFormModel->getResidenceProof();
        $residenceProofDirectory = $filesDirectory["residence_directory"];
        $fileUploader->setTargetDirectory($residenceProofDirectory);
        $residenceProofFileName = $fileUploader->upload($residenceProof);
        $user->setResidenceProof($residenceProofFileName);
        return $user;
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
    ): User {
        // Get data from form
        $userFormModel = $form->getData();
        $user = new User();
        // Hydrate User
        $user = $this->initializeUser($user, $userFormModel);
        // Save files
        $user = $this->saveFiles($user, $fileUploader, $userFormModel, $filesDirectory);
        // Encode the plain password
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $userFormModel->getPlainPassword()
            )
        );
        // Delete the plain password
        $user->eraseCredentials();
        // Delete passwords in model
        $userFormModel->erasePasswords();
        // Set user roles
        $user->setRoles(['ROLE_USER']);
        // Set user relations
        $this->createUserWallet($user);
        $user->setLanguage($userLanguage);
        // Verify user email
        //$this->verifyEmail($emailVerifier, $user);
        // Persist user
        $entityManager->persist($user);
        $entityManager->flush();
        return $user;
    }

    private function createUserWallet(User $user): void
    {
        $userWallet = new Wallet();
        $userWallet
            ->setUser($user)
            ->setAmount(0);
        $user->setWallet($userWallet);
    }

    private function verifyEmail(EmailVerifier $emailVerifier, User $user): void
    {
        // generate a signed url and email it to the user
        $emailVerifier->sendEmailConfirmation(
            'account_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('confirmation@bet-project.com', 'Confirmation Mail'))
                ->to((string) $user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
        // do anything else you need here, like send an email
    }
}
