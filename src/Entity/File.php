<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileRepository::class)]
#[ORM\HasLifecycleCallbacks]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'files')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    #[ORM\Column(length: 255)]
    private ?string $file_path = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->file_path;
    }

    public function setFilePath(string $file_path): static
    {
        $this->file_path = $file_path;

        return $this;
    }

    //Deletes the file from the disk when the entity is removed
    #[ORM\PreRemove]
    public function deleteFileFromDisk(): void
    {
        
        $uploadDir = __DIR__ . '/../../public/uploads';
        $filePath = $uploadDir . '/' . basename($this->file_path);

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
