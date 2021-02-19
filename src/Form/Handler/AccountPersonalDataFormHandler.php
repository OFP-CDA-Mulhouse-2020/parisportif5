<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\User;
use App\Form\Model\UserFormModel;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\FormInterface;

final class AccountPersonalDataFormHandler
{
    private function updateUser(
        User $user,
        UserFormModel $userFormModel
    ): void {
        $email = $userFormModel->getEmail();
        if ($email !== $user->getEmail()) {
            $user->setEmail($email);
        }
        $civility = $userFormModel->getCivility();
        if ($civility !== $user->getCivility()) {
            $user->setCivility($civility);
        }
        $firstName = $userFormModel->getFirstName();
        if ($firstName !== $user->getFirstName()) {
            $user->setFirstName($firstName);
        }
        $lastName = $userFormModel->getLastName();
        if ($lastName !== $user->getLastName()) {
            $user->setLastName($lastName);
        }
        $address = $userFormModel->getBillingAddress();
        if ($address !== $user->getBillingAddress()) {
            $user->setBillingAddress($address);
        }
        $city = $userFormModel->getBillingCity();
        if ($city !== $user->getBillingCity()) {
            $user->setBillingCity($city);
        }
        $postcode = $userFormModel->getBillingPostcode();
        if ($postcode !== $user->getBillingPostcode()) {
            $user->setBillingPostcode($postcode);
        }
        $country = $userFormModel->getBillingCountry();
        if ($country !== $user->getBillingCountry()) {
            $user->setBillingCountry($country);
        }
        $birthDate = $userFormModel->getBirthDate();
        if ($birthDate !== $user->getBirthDate()) {
            $user->setBirthDate($birthDate);
        }
        $timezone = $userFormModel->getTimeZoneSelected();
        if ($timezone !== $user->getTimeZoneSelected()) {
            $user->setTimeZoneSelected($timezone);
        }
        $newletters = $userFormModel->getNewsletters();
        if ($newletters !== $user->getNewsletters()) {
            $user->setNewsletters($newletters);
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
