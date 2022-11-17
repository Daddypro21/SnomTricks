<?php

namespace App\Entity;

use App\Repository\ImageupdateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageupdateRepository::class)]
class Imageupdate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageupdate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageupdate(): ?string
    {
        return $this->imageupdate;
    }

    public function setImageupdate(?string $imageupdate): self
    {
        $this->imageupdate = $imageupdate;

        return $this;
    }
}
