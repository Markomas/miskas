<?php

namespace App\Repository;

use App\Entity\StorageFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StorageFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method StorageFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method StorageFile[]    findAll()
 * @method StorageFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StorageFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StorageFile::class);
    }

    // /**
    //  * @return StorageFile[] Returns an array of StorageFile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StorageFile
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
