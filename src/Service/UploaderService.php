<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploaderService
{
    private SluggerInterface $slugger;
    private string $uploadDirectory;

    public function __construct(SluggerInterface $slugger, string $uploadDirectory)
    {
        $this->slugger = $slugger;
        $this->uploadDirectory = $uploadDirectory;
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid('', true) . '.' . $file->guessExtension();

        $file->move($this->uploadDirectory, $fileName);

        return $fileName;
    }

    public function remove(string $filename): void
    {
        unlink($this->uploadDirectory . DIRECTORY_SEPARATOR . $filename);
    }
}