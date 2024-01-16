<?php

namespace App\Repository;

use App\Entity\ListaReproduccion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ListaReproduccion>
 *
 * @method ListaReproduccion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListaReproduccion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListaReproduccion[]    findAll()
 * @method ListaReproduccion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListaReproduccionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListaReproduccion::class);
    }

//    /**
//     * @return ListaReproduccion[] Returns an array of ListaReproduccion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ListaReproduccion
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
