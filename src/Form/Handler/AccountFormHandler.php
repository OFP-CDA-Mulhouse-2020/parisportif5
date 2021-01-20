<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\User;
use App\Form\Account\AccountDocumentFormType;
use App\Form\Account\AccountUpdateIdentifierFormType;
use App\Form\Account\AccountUpdatePasswordFormType;
use App\Security\EmailVerifier;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class AccountFormHandler
{
    public function handleForm(
        FormInterface $form,
        User $user,
        ObjectManager $entityManager,
        ?UserPasswordEncoderInterface $passwordEncoder = null,
        ?EmailVerifier $emailVerifier = null
    ): void {
        // get data from form
        $user = $form->getData();
        if ($form instanceof AccountDocumentFormType) {
            // files directories
            $user->setIdentityDocument('filename.pdf');
            $user->setResidenceProof('filename.pdf');
        }
        if ($form instanceof AccountUpdatePasswordFormType) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $user->getPlainPassword()
                )
            );
            // delete the plain password
            $user->eraseCredentials();
        }
        if ($form instanceof AccountUpdateIdentifierFormType) {
            $user->setIsVerified(false);
            // Verify new user email
            //$this->verifyEmail($emailVerifier, $user);
        }
        // Persist update user
        $entityManager->persist($user);
        $entityManager->flush();
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
