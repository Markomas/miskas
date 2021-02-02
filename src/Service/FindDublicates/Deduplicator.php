<?php


namespace App\Service\FindDublicates;


use App\Entity\StorageFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\LockFactory;

class Deduplicator
{
    /**
     * @var LockFactory
     */
    private LockFactory $lockFactory;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    private \App\Repository\StorageFileRepository|\Doctrine\Persistence\ObjectRepository $storageFileRepository;

    public function __construct(LockFactory $lockFactory, EntityManagerInterface $entityManager) {
        $this->lockFactory = $lockFactory;
        $this->entityManager = $entityManager;
        $this->storageFileRepository = $this->entityManager->getRepository(StorageFile::class);
    }

    public function scan()
    {
        $allFiles = $this->storageFileRepository->findBy(['isSkipped' => false]);
        foreach ($allFiles as $file) {
            //$file
        }
    }
}