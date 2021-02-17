<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader
{
    private string $targetDirectory;
    private SluggerInterface $slugger;

    public function __construct(
        SluggerInterface $slugger,
        string $targetDirectory
    ) {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function getFileName(UploadedFile $file): string
    {
        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFileName = $this->slugger->slug($originalFileName);
        $newFileName = $safeFileName . '-' . uniqid() . '.' . $file->guessExtension();
        return $newFileName;
    }

    public function upload(UploadedFile $file): string
    {
        $fileName = $this->getFileName($file);
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
        if (\mb_strpos($testedDirectory, '\\uploads\\') === false) {
            return false;
        }
        return \file_exists($testedDirectory);
    }

    public function setTargetDirectory(string $targetDirectoryInUploads): void
    {
        if ($this->isValidDirectory($targetDirectoryInUploads) === true) {
            $this->targetDirectory = $targetDirectoryInUploads;
        }
    }
}
