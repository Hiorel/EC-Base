<?php

namespace App\Repository;

use App\Entity\Screenshots;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Screenshots|null find($id, $lockMode = null, $lockVersion = null)
 * @method Screenshots|null findOneBy(array $criteria, array $orderBy = null)
 * @method Screenshots[]    findAll()
 * @method Screenshots[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScreenshotsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Screenshots::class);
    }

    // /**
    //  * @return Screenshots[] Returns an array of Screenshots objects
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
    public function findOneBySomeField($value): ?Screenshots
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
