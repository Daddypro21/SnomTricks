<?php

namespace App\EventListener\Doctrine;

use App\Entity\Images;
use App\Service\UploaderService;

class ImageUploadListener
{
    private UploaderService $uploaderService;

    public function __construct(UploaderService $uploaderService)
    {
        $this->uploaderService = $uploaderService;
    }

    public function prePersist(Images $image): void
    {
        if (!$image->getFile()) {
            return;
        }

        $image->setFilename(
            $this->uploaderService->upload(
                $image->getFile()
            )
        );
    }

    public function postRemove(Images $image): void
    {
        $this->uploaderService->remove($image->getFilename());
    }
}