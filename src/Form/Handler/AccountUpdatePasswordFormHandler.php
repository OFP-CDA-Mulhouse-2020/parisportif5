<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class AccountUpdatePasswordFormHandler
{
    public function handleForm(
        FormInterface $form,
        User $user,
        ObjectManager $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ): void {
        // Get data from form
        $userFormModel = $form->getData();
        // encode the plain password
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $userFormModel->getNewPassword()
            )
        );
        // Delete the plain password
        $user->eraseCredentials();
        // Delete passwords in model
        $userFormModel->erasePasswords();
        // Save modification
        $entityManager->flush();
    }
}
