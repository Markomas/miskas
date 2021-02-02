<?php


namespace App\Service\Torrent;


use App\Repository\StorageSpaceRepository;
use App\Service\MovieDB\MovieDB;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Process\Process;

class Downloader
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var StorageSpaceRepository
     */
    private StorageSpaceRepository $storageSpaceRepository;
    /**
     * @var MovieDB
     */
    private MovieDB $movieDB;
    /**
     * @var LockFactory
     */
    private LockFactory $lockFactory;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    private \Symfony\Component\Lock\LockInterface $lock;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, LockFactory $lockFactory, MovieDB $movieDB, StorageSpaceRepository $storageSpaceRepository) {
        $this->entityManager = $entityManager;
        $this->storageSpaceRepository = $storageSpaceRepository;
        $this->movieDB = $movieDB;
        $this->lockFactory = $lockFactory;
        $this->logger = $logger;
    }

    public function run()
    {
        $offset = 0;
        $storageSpace = $this->storageSpaceRepository->findNextToDownload($offset);
        $this->lock = $this->lockFactory->createLock('downloader_download_lock_'.$storageSpace->getId());
        if(!$this->lock->acquire()) {
            $offset++;
            $storageSpace = $this->storageSpaceRepository->findNextToDownload($offset);
            $this->lock = $this->lockFactory->createLock('downloader_download_'.$storageSpace->getId());
        }

        $torrent = $this->movieDB->getTorrentFilePath($storageSpace->getSpace());
        $destination = $this->movieDB->getDownloadPath($storageSpace->getSpace());

        try {
            $this->logger->info('Torrent downloader: starting download: ' . $storageSpace->getId());
            $this->download($torrent, $destination);
            $storageSpace->setIsDownloaded(true);
            $this->entityManager->persist($storageSpace);
            $this->entityManager->flush();
            $this->logger->info('Torrent downloader: download completed: ' . $storageSpace->getId());
        } catch (RuntimeException $e) {
            $this->logger->error('Torrent downloader: download failed:' . $storageSpace->getId(). ' ' . $e->getMessage());
        }

    }

    private function download(string $torrent, string $destination)
    {
        $command = ['aria2c', '--seed-time=0', '--disk-cache=160M', '--console-log-level=info', '--continue=true', $torrent, '-d', $destination];
        $process = new Process($command);
        $process->setTimeout(60*60*24*2);
        $process->setIdleTimeout(60);
        $lock = $this->lock;
        $process->run(function ($type, $buffer) use ($lock) {
            $lock->refresh();
        });
    }


}