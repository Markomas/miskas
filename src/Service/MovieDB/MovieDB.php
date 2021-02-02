<?php


namespace App\Service\MovieDB;

use App\Service\Crawler\Model\TorrentModel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

class MovieDB
{
    private string $rootDirectory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var Database
     */
    private Database $db;
    private LockInterface $lock;

    public function __construct(string $rootDirectory, LockFactory $lockFactory, Database $db, LoggerInterface $logger) {
        $this->rootDirectory = rtrim($rootDirectory, DIRECTORY_SEPARATOR);
        $this->logger = $logger;
        $this->db = $db;
        $this->db->setRootDirectory($this->rootDirectory);
        $this->lock = $lockFactory->createLock('movie_database');
    }

    public function saveTorrent(TorrentModel $torrent)
    {
        while(!$this->lock->acquire()) {
            $this->logger->warning('MovieDB: db locked, waiting');
            sleep(10);
        }
        $this->db->insertTorrent($torrent);
        $this->lock->release();
    }

    public function getTorrentFilePath(string $path): ?string {
        return $this->db->getTorrentFilePath($path);
    }

    public function getValueFromPath(string $path, string $name): ?array {
        return $this->db->loadValue($path, $name);
    }

    public function saveStorageFile(array $storageFile, string $path)
    {
        while(!$this->lock->acquire()) {
            $this->logger->warning('MovieDB: db locked, waiting');
            sleep(10);
        }
        $this->db->insertStorageFile($storageFile, $path);
        $this->lock->release();
    }

    public function getDownloadPath(?string $path): ?string
    {
        return $this->db->getDownloadPath($path);
    }
}