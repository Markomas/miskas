<?php

namespace App\Entity;

use App\Repository\StorageFileRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass=StorageFileRepository::class)
 */
class StorageFile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $extension;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $season;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $episode;

    /**
     * @ORM\Column(type="float")
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $source;

    /**
     * @ORM\Column(type="integer")
     */
    private $releaseQuality;

    /**
     * @ORM\Column(type="integer")
     */
    private $resolution;

    /**
     * @ORM\Column(type="json")
     */
    private $languages = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSkipped = false;

    /**
     * @ORM\ManyToOne(targetEntity=StorageSpace::class, inversedBy="storageFiles")
     */
    private $storage;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $path;

    /**
     * @ORM\ManyToOne(targetEntity=Movie::class, inversedBy="storageFile")
     */
    private $movie;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getSeason(): ?int
    {
        return $this->season;
    }

    public function setSeason(?int $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getEpisode(): ?int
    {
        return $this->episode;
    }

    public function setEpisode(?int $episode): self
    {
        $this->episode = $episode;

        return $this;
    }

    public function getSize(): ?float
    {
        return $this->size;
    }

    public function setSize(float $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getReleaseQuality(): ?int
    {
        return $this->releaseQuality;
    }

    public function setReleaseQuality(int $releaseQuality): self
    {
        $this->releaseQuality = $releaseQuality;

        return $this;
    }

    public function getResolution(): ?int
    {
        return $this->resolution;
    }

    public function setResolution(int $resolution): self
    {
        $this->resolution = $resolution;

        return $this;
    }

    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    public function setLanguages(array $languages): self
    {
        $this->languages = $languages;

        return $this;
    }

    #[Pure] public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function getIsSkipped(): ?bool
    {
        return $this->isSkipped;
    }

    public function setIsSkipped(bool $isSkipped): self
    {
        $this->isSkipped = $isSkipped;

        return $this;
    }

    public function getStorage(): ?StorageSpace
    {
        return $this->storage;
    }

    public function setStorage(?StorageSpace $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function loadFromArray($data) {
        foreach($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }
}
