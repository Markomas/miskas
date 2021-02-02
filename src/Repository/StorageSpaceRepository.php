<?php

namespace App\Repository;

use App\Entity\StorageSpace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StorageSpace|null find($id, $lockMode = null, $lockVersion = null)
 * @method StorageSpace|null findOneBy(array $criteria, array $orderBy = null)
 * @method StorageSpace[]    findAll()
 * @method StorageSpace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StorageSpaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StorageSpace::class);
    }

    // /**
    //  * @return StorageSpace[] Returns an array of StorageSpace objects
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
    public function findOneBySomeField($value): ?StorageSpace
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findNextToDownload($offset = 0): ?StorageSpace
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.storageFiles', 'f')
            ->andWhere('s.isDownloaded = :isDownloaded')
            ->andWhere('s.isScanned = :isScanned')
            ->andWhere('s.isSkiped = :isSkiped')
            ->andWhere('f.isSkipped = :isSkipped')
            ->setParameter('isDownloaded', false)
            ->setParameter('isScanned', true)
            ->setParameter('isSkiped', false)
            ->setParameter('isSkipped', false)
            ->orderBy('s.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
