<?php

declare(strict_types=1);

namespace App\Form\Handler;

use App\Entity\User;
use App\Service\FileUploader;
use App\Form\Model\UserFormModel;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class AccountDocumentFormHandler
{
    public static function getBasenameFromFormated(string $filename): string
    {
        $filename = \preg_replace('/^(.*)(\-[a-z\d\.]*)(\.[a-z]*)$/i', "$1$3", $filename);
        return \is_string($filename) ? $filename : "";
    }

    private function updateIdentityDocumentFile(
        User $user,
        UploadedFile $identityDocument,
        FileUploader $fileUploader,
        string $identityDocumentDirectory
    ): void {
        if ($fileUploader->getSafeFileName($identityDocument) !== self::getBasenameFromFormated($user->getIdentityDocument())) {
            $fileUploader->setTargetDirectory($identityDocumentDirectory);
            $identityDocumentFileName = $fileUploader->upload($identityDocument);
            $user->setIdentityDocument($identityDocumentFileName);
        }
    }

    private function updateResidenceProofFile(
        User $user,
        UploadedFile $residenceProof,
        FileUploader $fileUploader,
        string $residenceProofDirectory
    ): void {
        if ($fileUploader->getSafeFileName($residenceProof) !== self::getBasenameFromFormated($user->getResidenceProof())) {
            $fileUploader->setTargetDirectory($residenceProofDirectory);
            $residenceProofFileName = $fileUploader->upload($residenceProof);
            $user->setResidenceProof($residenceProofFileName);
        }
    }

    /** @param string[] $filesDirectories */
    public function handleForm(
        FormInterface $form,
        User $user,
        ObjectManager $entityManager,
        FileUploader $fileUploader,
        array $filesDirectories
    ): void {
        // Get data from form
        /** @var UserFormModel $userFormModel */
        $userFormModel = $form->getData();
        // Hydrate User
        $identityDocument = $userFormModel->getIdentityDocument();
        if ($identityDocument !== null) {
            $this->updateIdentityDocumentFile(
                $user,
                $identityDocument,
                $fileUploader,
                $filesDirectories["identity_directory"]
            );
        }
        $residenceProof = $userFormModel->getResidenceProof();
        if ($residenceProof !== null) {
            $this->updateResidenceProofFile(
                $user,
                $residenceProof,
                $fileUploader,
                $filesDirectories["residence_directory"]
            );
        }
        // Delete passwords in model
        $userFormModel->erasePasswords();
        // Save modification
        $entityManager->flush();
    }
}
