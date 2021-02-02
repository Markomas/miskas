<?php


namespace App\Service\Torrent;


use App\Entity\Movie;
use App\Entity\StorageFile;
use App\Entity\StorageSpace;
use App\Repository\MovieRepository;
use App\Service\MovieDB\MovieDB;
use App\Service\Parser\ReleaseParser;
use App\Service\Torrent\Util\TorrentParser;
use Doctrine\ORM\EntityManagerInterface;

class Scanner
{
    private array $videoExtensions = [
        'mkv',
        'avi',
        'mp4',
        'webm',
        'wmv',
        'm4v',
    ];
    /**
     * @var ReleaseParser
     */
    private ReleaseParser $parser;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var MovieDB
     */
    private MovieDB $movieDB;
    private MovieRepository $movieRepository;

    public function __construct(ReleaseParser $parser, MovieDB $movieDB, EntityManagerInterface $entityManager)
    {
        $this->parser = $parser;
        $this->entityManager = $entityManager;
        $this->movieDB = $movieDB;
        $this->movieRepository = $this->entityManager->getRepository(Movie::class);
    }

    public function scan(): bool
    {
        $storageSpace = $this->entityManager
            ->getRepository(StorageSpace::class)
            ->findOneBy(['isScanned' => false]);
        //->findOneBy(['space' => '/app/var/data/data2/0f/46ffd5702048b0c3c31c3e305590cab3f48d54e6ef1189b3667efde4d65003']);

        if (!$storageSpace) {
            return false;
        }

        $spacePath = $storageSpace->getSpace();

        $path = $this->movieDB->getTorrentFilePath($spacePath);
        $torrentValues = $this->movieDB->getValueFromPath($spacePath, 'torrent');

        $torrentParser = new TorrentParser($path);

        $storageFiles = [];

        foreach ($torrentParser->content() as $file => $size) {
            $title = $this->parser->parse($file);
            $storageFile = $this->entityManager->getRepository(StorageFile::class)->findOneBy(['path' => $file]);
            if (!$storageFile) {
                $storageFile = new StorageFile();
            }

            $title->setSize($size);
            $storageFile->setTitle($title->getTitle());
            $storageFile->setSeason($title->getSeason());
            $storageFile->setEpisode($title->getEpisode());
            $storageFile->setExtension($title->getExtension());
            $storageFile->setSize($title->getSize());
            $storageFile->setSource($title->getSource());
            $storageFile->setReleaseQuality($title->getReleaseQuality());
            $storageFile->setResolution($title->getResolution());
            $storageFile->setLanguages($title->getLanguages());
            $storageFile->setStorage($storageSpace);
            $storageFile->setPath($file);
            $this->entityManager->persist($storageFile);
            $storageFiles[$file] = $storageFile;
        }

        $storageSpace->setIsScanned(true);
        $this->entityManager->persist($storageSpace);
        $this->movieDB->saveStorageFile($storageFiles, $spacePath);
        $this->entityManager->flush();

        $this->createMovies($storageFiles, $torrentValues);

        return true;
    }

    private function createMovies(array $storageFiles, array $torrentValues)
    {
        $imdb = $torrentValues['imdb'];
        foreach ($storageFiles as $storageFile) {
            if(!in_array($storageFile->getExtension(), $this->videoExtensions)) {
                $storageFile->setIsSkipped(true);
                $this->entityManager->persist($storageFile);
                continue;
            }

            $movie = $this->movieRepository->findOneByImdbSeasonEpisode(
                $imdb,
                $storageFile->getSeason(),
                $storageFile->getEpisode()
            );

            if (!$movie) {
                $movie = new Movie();
                $movie->setImdb($imdb);
                $movie->setSeason($storageFile->getSeason());
                $movie->setEpisode($storageFile->getEpisode());
            } else {
                foreach ($movie->getStorageFile() as $existingStorageFile) {
                    if ($existingStorageFile->getId() === $storageFile->getId()) {
                        continue;
                    }

                    if($existingStorageFile->getIsSkipped()) {
                        continue;
                    }

                    if ($existingStorageFile->getReleaseQuality() > $storageFile->getReleaseQuality()) {
                        $storageFile->setIsSkipped(true);
                        $this->entityManager->persist($storageFile);
                        continue;
                    } elseif ($existingStorageFile->getReleaseQuality() === $storageFile->getReleaseQuality()
                        && $existingStorageFile->getResolution() > $storageFile->getResolution()) {
                        $storageFile->setIsSkipped(true);
                        $this->entityManager->persist($storageFile);
                        continue;
                    } elseif ($existingStorageFile->getReleaseQuality() === $storageFile->getReleaseQuality()
                        && $existingStorageFile->getResolution() === $storageFile->getResolution()
                        && $existingStorageFile->getSize() >= $storageFile->getSize()
                    ) {
                        $storageFile->setIsSkipped(true);
                        $this->entityManager->persist($storageFile);
                        continue;
                    }

                    $existingStorageFile->setIsSkipped(true);
                    $this->entityManager->persist($existingStorageFile);
                }
            }

            $movie->addStorageFile($storageFile);
            $this->entityManager->persist($movie);
            $this->entityManager->flush();
        }
    }
}