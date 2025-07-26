<?php

namespace App\Repository;

use App\Entity\Poussin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Poussin>
 *
 * @method Poussin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Poussin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Poussin[]    findAll()
 * @method Poussin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PoussinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poussin::class);
    }

//    /**
//     * @return Poussin[] Returns an array of Poussin objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Poussin
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
