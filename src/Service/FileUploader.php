<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\AbstractUnicodeString;

class FileUploader
{
    private string $targetDirectory;
    private string $defaultDirectory;
    private SluggerInterface $slugger;

    public function __construct(
        SluggerInterface $slugger,
        string $targetDirectory
    ) {
        $this->targetDirectory = $targetDirectory;
        $this->defaultDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function getSafeFileName(UploadedFile $file): AbstractUnicodeString
    {
        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFileName = $this->slugger->slug($originalFileName);
        return $safeFileName;
    }

    public function getFormatedFileName(UploadedFile $file): string
    {
        $safeFileName = $this->getSafeFileName($file);
        $newFileName = $safeFileName . '-' . uniqid() . '.' . $file->guessExtension();
        return $newFileName;
    }

    public function upload(UploadedFile $file): string
    {
        $fileName = $this->getFormatedFileName($file);
        $targetDirectory = $this->getTargetDirectory();

        try {
            $file->move($targetDirectory, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            return '';
        }

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    private function isValidDirectory(string $testedDirectory): bool
    {
        if (\mb_strpos($testedDirectory, $this->defaultDirectory) === false) {
            return false;
        }
        return \file_exists($testedDirectory);
    }

    public function setTargetDirectory(string $targetDirectoryInUploads): void
    {
        $this->targetDirectory = $this->defaultDirectory;
        if ($this->isValidDirectory($targetDirectoryInUploads) === true) {
            $this->targetDirectory = $targetDirectoryInUploads;
        }
    }
}
