<?php


namespace App\Service\Parser\DTO;


use JetBrains\PhpStorm\Pure;

class TitleDTO
{
    private $title = '';
    private $extension = '';
    private $season = null;
    private $episode = null;
    private $size = 0;
    private $source = '';
    private $releaseQuality = 0;
    private $resolution = 320;
    private $languages = [];

    /**
     * @return int
     */
    public function getResolution(): int
    {
        return $this->resolution;
    }

    /**
     * @param int $resolution
     */
    public function setResolution(int $resolution): void
    {
        $this->resolution = $resolution;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     */
    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    /**
     * @return null|integer
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @param null|integer $season
     */
    public function setSeason($season): void
    {
        $this->season = $season;
    }

    /**
     * @return null|integer
     */
    public function getEpisode()
    {
        return $this->episode;
    }

    /**
     * @param null|integer $episode
     */
    public function setEpisode($episode): void
    {
        $this->episode = $episode;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    /**
     * @return int
     */
    public function getReleaseQuality(): int
    {
        return $this->releaseQuality;
    }

    /**
     * @param int $releaseQuality
     */
    public function setReleaseQuality(int $releaseQuality): void
    {
        $this->releaseQuality = $releaseQuality;
    }

    /**
     * @return array
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * @param array $languages
     */
    public function setLanguages(array $languages): void
    {
        $this->languages = $languages;
    }
}