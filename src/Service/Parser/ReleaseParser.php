<?php


namespace App\Service\Parser;


use App\Service\Parser\DTO\TitleDTO;
use Psr\Log\LoggerInterface;
use thcolin\SceneReleaseParser\Release;
use thcolin\SceneReleaseParser\ReleaseConstants;

class ReleaseParser
{
    public array $sourceByQuality = [
        ReleaseConstants::SOURCE_CAM => 10,
        ReleaseConstants::SOURCE_TC => 20,
        ReleaseConstants::SOURCE_R5 => 30,
        ReleaseConstants::SOURCE_DVDSCR => 40,
        ReleaseConstants::SOURCE_SDTV => 50,
        ReleaseConstants::SOURCE_PDTV => 60,
        ReleaseConstants::SOURCE_DVD_R => 70,
        ReleaseConstants::SOURCE_DVDRIP => 80,
        ReleaseConstants::SOURCE_BDSCR => 90,
        ReleaseConstants::SOURCE_HDRIP => 100,
        ReleaseConstants::SOURCE_HDTV => 110,
        ReleaseConstants::SOURCE_WEB_DL => 120,
        ReleaseConstants::SOURCE_BLURAY => 125,
        ReleaseConstants::SOURCE_BDRIP => 130,
        ReleaseConstants::SOURCE_BDRemux => 140,
    ];

    public array $resolution = [
        ReleaseConstants::RESOLUTION_SD => 480,
        ReleaseConstants::RESOLUTION_720P => 720,
        ReleaseConstants::RESOLUTION_1080P => 1080,
        ReleaseConstants::RESOLUTION_1440P => 1444,
        ReleaseConstants::RESOLUTION_2160P => 2160,
        ReleaseConstants::RESOLUTION_3240P => 3240,
        ReleaseConstants::RESOLUTION_4320P => 4320
    ];
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function parse(string $file): TitleDTO
    {
        $defaults = [
            ReleaseConstants::SOURCE => ReleaseConstants::SOURCE_R5,
            ReleaseConstants::RESOLUTION => ReleaseConstants::RESOLUTION_SD
        ];

        $title = new TitleDTO();
        $title->setExtension($this->getExtension($file));

        try {
            $release = new Release($file, true, $defaults);
            $title->setTitle($release->getTitle());
            $title->setSource($release->getSource() ?? 'unknown');
            $title->setEpisode($release->getEpisode());
            $title->setSeason($release->getSeason());
            $releaseQuality = $this->sourceByQuality[$title->getSource()] ?? 30;
            $title->setReleaseQuality($releaseQuality);
            $resolution = $this->resolution[$release->getResolution()] ?? 320;
            $title->setResolution($resolution);
            $title->setLanguages($release->getMultiLanguage());
        } catch (\Exception $e) {
            $title->setTitle($file);
            $title->setReleaseQuality(30);
            $title->setResolution(320);
            $title->setSource('unknown');
            if(preg_match('/season\s*-?\s*(\d{1,3})\D+(\d{1,3})/', strtolower($file), $match)) {
                $title->setSeason(intval($match[1]));
                $title->setEpisode(intval($match[2]));
            }
            $this->logger->warning('Realease title parser: failed to use default parser for title: ' . $file);
        }

        return $title;
    }

    private function getExtension(string $name)
    {
        return pathinfo($name, PATHINFO_EXTENSION);
    }
}