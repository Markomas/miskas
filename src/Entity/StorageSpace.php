<?php

namespace App\Entity;

use App\Repository\StorageSpaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=StorageSpaceRepository::class)
 * @UniqueEntity("space")
 */
class StorageSpace
{
    const TYPE_TORRENT = 'torrent';
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private string $space;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private string $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isSkiped = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isDownloaded = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isScanned = false;

    /**
     * @ORM\OneToMany(targetEntity=StorageFile::class, mappedBy="storage")
     */
    private $storageFiles;

    public function __construct()
    {
        $this->storageFiles = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSpace(): ?string
    {
        return $this->space;
    }

    public function setSpace(string $space): self
    {
        $this->space = $space;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIsSkiped(): ?bool
    {
        return $this->isSkiped;
    }

    public function setIsSkiped(bool $isSkiped): self
    {
        $this->isSkiped = $isSkiped;

        return $this;
    }

    public function getIsDownloaded(): ?bool
    {
        return $this->isDownloaded;
    }

    public function setIsDownloaded(bool $isDownloaded): self
    {
        $this->isDownloaded = $isDownloaded;

        return $this;
    }

    public function getIsScanned(): ?bool
    {
        return $this->isScanned;
    }

    public function setIsScanned(bool $isScanned): self
    {
        $this->isScanned = $isScanned;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|StorageFile[]
     */
    public function getStorageFiles(): Collection
    {
        return $this->storageFiles;
    }

    public function addStorageFile(StorageFile $storageFile): self
    {
        if (!$this->storageFiles->contains($storageFile)) {
            $this->storageFiles[] = $storageFile;
            $storageFile->setStorage($this);
        }

        return $this;
    }

    public function removeStorageFile(StorageFile $storageFile): self
    {
        if ($this->storageFiles->removeElement($storageFile)) {
            // set the owning side to null (unless already changed)
            if ($storageFile->getStorage() === $this) {
                $storageFile->setStorage(null);
            }
        }

        return $this;
    }
}
