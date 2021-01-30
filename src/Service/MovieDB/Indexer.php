<?php


namespace App\Service\MovieDB;


use App\Service\MovieDB\Model\StorageModel;
use App\Service\MovieDB\Model\StorageModelArray;
use Psr\Log\LoggerInterface;

class Indexer
{
    private string $rootDirectory;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var Lock
     */
    private Lock $lock;
    /**
     * @var StorageModelArray
     */
    private StorageModelArray $storageList;

    public function __construct(string $rootDirectory, Lock $lock, LoggerInterface $logger) {
        $this->rootDirectory = rtrim($rootDirectory, DIRECTORY_SEPARATOR);
        $this->logger = $logger;
        $this->lock = $lock;
    }

    public function reindex()
    {
        $this->lock->lock();
        $this->scanStorage();
        $this->scanEntities();
        $this->lock->unlock();
    }

    private function scanStorage()
    {
        $directories = glob($this->rootDirectory . '/*' , GLOB_ONLYDIR);
        $this->storageList = new StorageModelArray();
        foreach ($directories as $directory) {
            $this->storageList[$directory] = new StorageModel($directory);
        }
    }

    private function scanEntities()
    {
        foreach ($this->storageList as $storage) {
            foreach($storage->getAll() as $entity) {

            }
        }
    }
}