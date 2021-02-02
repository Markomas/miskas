<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MovieRepository::class)
 */
class Movie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $imdb;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tmdb;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $season;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $episode;

    /**
     * @ORM\OneToMany(targetEntity=StorageFile::class, mappedBy="movie")
     */
    private $storageFile;

    public function __construct()
    {
        $this->storageFile = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImdb(): ?string
    {
        return $this->imdb;
    }

    public function setImdb(?string $imdb): self
    {
        $this->imdb = $imdb;

        return $this;
    }

    public function getTmdb(): ?int
    {
        return $this->tmdb;
    }

    public function setTmdb(?int $tmdb): self
    {
        $this->tmdb = $tmdb;

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

    /**
     * @return Collection|StorageFile[]
     */
    public function getStorageFile(): Collection
    {
        return $this->storageFile;
    }

    public function addStorageFile(StorageFile $storageFile): self
    {
        if (!$this->storageFile->contains($storageFile)) {
            $this->storageFile[] = $storageFile;
            $storageFile->setMovie($this);
        }

        return $this;
    }

    public function removeStorageFile(StorageFile $storageFile): self
    {
        if ($this->storageFile->removeElement($storageFile)) {
            // set the owning side to null (unless already changed)
            if ($storageFile->getMovie() === $this) {
                $storageFile->setMovie(null);
            }
        }

        return $this;
    }
}
