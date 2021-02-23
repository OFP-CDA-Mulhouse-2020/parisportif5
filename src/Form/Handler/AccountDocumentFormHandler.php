<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\User;
use App\Service\FileUploader;
use App\Form\Model\UserFormModel;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\FormInterface;

final class AccountDocumentFormHandler
{
    private function getBasenameFromFormated(string $filename): string
    {
        $filename = \preg_replace('/^(.*)(\-[a-z\d\.]*)(\.[a-z]*)$/i', "$1$3", $filename);
        return \is_string($filename) ? $filename : "";
    }

    /** @param string[] $filesDirectory */
    private function updateUser(
        User $user,
        UserFormModel $userFormModel,
        FileUploader $fileUploader,
        array $filesDirectory
    ): void {
        $identityDocument = $userFormModel->getIdentityDocument();
        if ($fileUploader->getSafeFileName($identityDocument) !== $this->getBasenameFromFormated($user->getIdentityDocument())) {
            $identityDocumentDirectory = $filesDirectory["identity_directory"];
            $fileUploader->setTargetDirectory($identityDocumentDirectory);
            $identityDocumentFileName = $fileUploader->upload($identityDocument);
            $user->setIdentityDocument($identityDocumentFileName);
        }
        $residenceProof = $userFormModel->getResidenceProof();
        if ($fileUploader->getSafeFileName($residenceProof) !== $this->getBasenameFromFormated($user->getResidenceProof())) {
            $residenceProofDirectory = $filesDirectory["residence_directory"];
            $fileUploader->setTargetDirectory($residenceProofDirectory);
            $residenceProofFileName = $fileUploader->upload($residenceProof);
            $user->setResidenceProof($residenceProofFileName);
        }
    }

    /** @param string[] $filesDirectory */
    public function handleForm(
        FormInterface $form,
        User $user,
        ObjectManager $entityManager,
        FileUploader $fileUploader,
        array $filesDirectory
    ): void {
        // Get data from form
        $userFormModel = $form->getData();
        // Hydrate User
        $this->updateUser($user, $userFormModel, $fileUploader, $filesDirectory);
        // Delete passwords in model
        $userFormModel->erasePasswords();
        // Save modification
        $entityManager->flush();
    }
}
