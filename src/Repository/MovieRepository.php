<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    public function findOneByImdbSeasonEpisode($imdb, $season, $episode): ?Movie {
        $qb = $this->createQueryBuilder('m');

        if(is_null($season)) {
            $qb->andWhere($qb->expr()->isNull('m.season'));
        } else {
            $qb->andWhere('m.season = :season')->setParameter('season', $season);
        }

        if(is_null($episode)) {
            $qb->andWhere($qb->expr()->isNull('m.episode'));
        } else {
            $qb->andWhere('m.episode = :episode')->setParameter('episode', $episode);
        }

        $qb->andWhere('m.imdb = :imdb')->setParameter('imdb', $imdb);


        return $qb->setMaxResults(1)->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return Movie[] Returns an array of Movie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Movie
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
