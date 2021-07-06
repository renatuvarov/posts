<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploadService
{
    private SluggerInterface $slugger;
    private string $targetDirectory;

    public function __construct(string $targetDirectory, SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-'. uniqid('', true) . '.' .$file->guessExtension();

        $file->move($this->targetDirectory, $fileName);

        return '/' . basename($this->targetDirectory) . '/' . $fileName;
    }
}