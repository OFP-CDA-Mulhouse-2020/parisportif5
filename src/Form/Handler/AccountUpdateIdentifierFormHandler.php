<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mime\Address;

final class AccountUpdateIdentifierFormHandler
{
    public function handleForm(
        FormInterface $form,
        User $user,
        ObjectManager $entityManager,
        EmailVerifier $emailVerifier
    ): void {
        // Get data from form
        $userFormModel = $form->getData();
        // Save email
        $user->setEmail($userFormModel->getNewEmail());
        // Reset verified value
        $user->setIsVerified(false);
        // Verify user email
        //$this->verifyEmail($emailVerifier, $user);
        // Delete passwords in model
        $userFormModel->erasePasswords();
        // Save modification
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
