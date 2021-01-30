<?php


namespace App\Service\Crawler\Model;


use JetBrains\PhpStorm\Pure;

class TorrentModel
{
    private ?string $id = null;
    private ?string $client = null;
    private ?string $file = null;
    private ?string $imdb = null;
    private ?string $title = null;
    private ?string $html = null;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getClient(): ?string
    {
        return $this->client;
    }

    /**
     * @param string|null $client
     */
    public function setClient(?string $client): void
    {
        $this->client = $client;
    }

    /**
     * @return string|null
     */
    public function getFile(): ?string
    {
        return $this->file;
    }

    /**
     * @param string|null $file
     */
    public function setFile(?string $file): void
    {
        $this->file = $file;
    }

    /**
     * @return string|null
     */
    public function getImdb(): ?string
    {
        return $this->imdb;
    }

    /**
     * @param string|null $imdb
     */
    public function setImdb(?string $imdb): void
    {
        $this->imdb = $imdb;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getHtml(): ?string
    {
        return $this->html;
    }

    /**
     * @param string|null $html
     */
    public function setHtml(?string $html): void
    {
        $this->html = $html;
    }

    #[Pure] public function toArray(): array
    {
        return get_object_vars($this);
    }

}