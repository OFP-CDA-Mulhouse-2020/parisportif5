<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\User;
use App\Form\Model\UserFormModel;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\FormInterface;

final class AccountParameterFormHandler
{
    private function updateUser(
        User $user,
        UserFormModel $userFormModel
    ): void {
        $newsletters = $userFormModel->getNewsletters();
        if ($newsletters !== $user->getNewsletters()) {
            $user->setNewsletters($newsletters);
        }
        $timezone = $userFormModel->getTimeZoneSelected();
        if ($timezone !== $user->getTimeZoneSelected()) {
            $user->setTimeZoneSelected($timezone);
        }
    }

    public function handleForm(
        FormInterface $form,
        User $user,
        ObjectManager $entityManager
    ): void {
        // Get data from form
        $userFormModel = $form->getData();
        // Hydrate User
        $this->updateUser($user, $userFormModel);
        // Delete passwords in model
        $userFormModel->erasePasswords();
        // Save modification
        $entityManager->flush();
    }
}
