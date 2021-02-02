<?php


namespace App\Service\MovieDB;


use App\Entity\StorageFile;
use App\Entity\StorageSpace;
use App\Repository\StorageFileRepository;
use App\Service\MovieDB\Model\SpaceModel;
use App\Service\MovieDB\Model\SpaceModelArray;
use App\Service\MovieDB\Model\StorageModel;
use App\Service\MovieDB\Model\StorageModelArray;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\LockFactory;

class Indexer
{
    private string $rootDirectory;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
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
    private StorageFileRepository $storageFileRepository;
    private \Symfony\Component\Lock\LockInterface $lock;

    public function __construct(string $rootDirectory, LockFactory $lockFactory, EntityManagerInterface $entityManager, LoggerInterface $logger) {
        $this->rootDirectory = rtrim($rootDirectory, DIRECTORY_SEPARATOR);
        $this->logger = $logger;
        $this->entities = new SpaceModelArray();
        $this->entityManager = $entityManager;
        $this->storageFileRepository = $this->entityManager->getRepository(StorageFile::class);
        $this->lock = $lockFactory->createLock('movie_database');
    }

    public function reindex()
    {
        while(!$this->lock->acquire()) {
            $this->logger->warning('MovieDB indexer: db locked, waiting');
            sleep(10);
        }
        $this->scanStorage();
        $this->scanEntities();
        $this->storeEntities();
        $this->reportUnusedEntities();
        $this->lock->release();
    }

    private function scanStorage()
    {
        $this->logger->info('MovieDB indexer: Scanning storage');
        $directories = glob($this->rootDirectory . '/*' , GLOB_ONLYDIR);
        $this->storageList = new StorageModelArray();
        foreach ($directories as $directory) {
            $this->lock->refresh();
            $this->storageList[$directory] = new StorageModel($directory);
        }
    }

    private function scanEntities()
    {
        $this->logger->info('MovieDB indexer: Scanning entities');
        foreach ($this->storageList as $storage) {
            foreach($storage->getAll() as $entity) {
                $this->lock->refresh();
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
            $this->lock->refresh();
            $this->add($entity);
        }
    }

    public function add(SpaceModel $model) {
        $storageSpace = $this->entityManager->getRepository(StorageSpace::class)->findOneBy(['space'=>$model->getPath()]);
        if(!$storageSpace) {
            $storageSpace = new StorageSpace();
            $storageSpace->setSpace($model->getPath());
        }

        if(in_array('torrent.json', $model->getTopFiles())) {
            $torrentData = $model->loadValue('torrent');
            $name = $torrentData['title'] ?? null;
            $storageSpace->setName($name);
            $storageSpace->setSpace($model->getPath());
            $storageSpace->setType(StorageSpace::TYPE_TORRENT);
        }

        if(in_array('files.json', $model->getTopFiles())) {
            $storageSpace->setIsScanned(true);
            $torrentData = $model->loadValue('files');
            foreach ($torrentData as $path => $torrentFile) {
                $storageFile = $this->storageFileRepository->findOneBy(['path' => $path, 'storage' => $storageSpace]);
                if(!$storageFile) {
                    $storageFile = new StorageFile();
                }
                $storageFile->setStorage($storageSpace);
                unset($torrentFile['storage']);
                $storageFile->loadFromArray($torrentFile);
                $this->entityManager->persist($storageFile);
            }
        }

        $this->entityManager->persist($storageSpace);
        $this->entityManager->flush();
    }

    private function reportUnusedEntities()
    {
        $this->logger->info('MovieDB indexer: Searching for unused entities');
        $storageSpace = $this->entityManager->getRepository(StorageSpace::class)->findAll();
        $this->logger->info('MovieDB indexer: Storage entities count:' . count($storageSpace));
        foreach($storageSpace as $space) {
            $this->lock->refresh();
            if(!isset($this->entities[$space->getSpace()])) {
                $this->logger->warning('MovieDB indexer: non-existing storage space in db: ' . $space->getId());
            }
        }
    }
}