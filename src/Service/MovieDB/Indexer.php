<?php


namespace App\Service\MovieDB;


use App\Entity\StorageSpace;
use App\Service\MovieDB\Model\SpaceModel;
use App\Service\MovieDB\Model\SpaceModelArray;
use App\Service\MovieDB\Model\StorageModel;
use App\Service\MovieDB\Model\StorageModelArray;
use Doctrine\ORM\EntityManagerInterface;
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
    /**
     * @var SpaceModelArray
     */
    private SpaceModelArray $entities;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(string $rootDirectory, Lock $lock, EntityManagerInterface $entityManager, LoggerInterface $logger) {
        $this->rootDirectory = rtrim($rootDirectory, DIRECTORY_SEPARATOR);
        $this->logger = $logger;
        $this->lock = $lock;
        $this->entities = new SpaceModelArray();
        $this->entityManager = $entityManager;
    }

    public function reindex()
    {
        $this->lock->lock();
        $this->scanStorage();
        $this->scanEntities();
        $this->storeEntities();
        $this->reportUnusedEntities();
        $this->lock->unlock();
    }

    private function scanStorage()
    {
        $this->logger->info('MovieDB indexer: Scanning storage');
        $directories = glob($this->rootDirectory . '/*' , GLOB_ONLYDIR);
        $this->storageList = new StorageModelArray();
        foreach ($directories as $directory) {
            $this->storageList[$directory] = new StorageModel($directory);
        }
    }

    private function scanEntities()
    {
        $this->logger->info('MovieDB indexer: Scanning entities');
        foreach ($this->storageList as $storage) {
            foreach($storage->getAll() as $entity) {
                if($entity->isEmpty()) {
                    $entity->remove();
                    continue;
                }
                $this->entities[$entity->getPath()] = $entity;
            }
        }
    }

    private function storeEntities()
    {
        $this->logger->info('MovieDB indexer: Storing entities');
        foreach ($this->entities as $entity) {
            $this->add($entity);
        }
    }

    public function add(SpaceModel $model) {
        $storageSpace = $this->entityManager->getRepository(StorageSpace::class)->findOneBy(['space'=>$model->getPath()]);
        if(!$storageSpace) {
            $storageSpace = new StorageSpace();
            $storageSpace->setSpace($model->getPath());
        }

        if(in_array('torrent.torrent', $model->getTopFiles())) {
            $storageSpace->setSpace($model->getPath());
            $storageSpace->setType(StorageSpace::TYPE_TORRENT);
        }

        $this->entityManager->persist($storageSpace);
        $this->entityManager->flush();
    }

    private function reportUnusedEntities()
    {
        $this->logger->info('MovieDB indexer: Searching for unused entities');
        $storageSpace = $this->entityManager->getRepository(StorageSpace::class)->findAll();
        foreach($storageSpace as $space) {
            if(!isset($this->entities[$space->getSpace()])) {
                $this->logger->warning('MovieDB indexer: non-existing storage space in db: ' . $space->getId());
            }
        }
    }
}