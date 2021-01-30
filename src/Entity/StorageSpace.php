<?php

namespace App\Entity;

use App\Repository\StorageSpaceRepository;
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
}
