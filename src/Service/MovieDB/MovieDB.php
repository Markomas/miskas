<?php


namespace App\Service\MovieDB;


use App\Service\Crawler\Model\TorrentModel;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class MovieDB
{
    private string $rootDirectory;
    /**
     * @var false|resource
     */
    private $dbLock;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var Database
     */
    private Database $db;
    /**
     * @var Lock
     */
    private Lock $lock;

    public function __construct(string $rootDirectory, Lock $lock, Database $db, LoggerInterface $logger) {
        $this->rootDirectory = rtrim($rootDirectory, DIRECTORY_SEPARATOR);
        $this->logger = $logger;
        $this->db = $db;
        $this->db->setRootDirectory($this->rootDirectory);
        $this->lock = $lock;
    }

    public function saveTorrent(TorrentModel $torrent)
    {
        $this->lock->lock();
        $this->db->insertTorrent($torrent);
        $this->lock->unlock();
    }


}