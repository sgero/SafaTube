<?php

namespace App\Repository;

use App\Entity\TipoPrivacidad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipoPrivacidad>
 *
 * @method TipoPrivacidad|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoPrivacidad|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoPrivacidad[]    findAll()
 * @method TipoPrivacidad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoPrivacidadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoPrivacidad::class);
    }

//    /**
//     * @return TipoPrivacidad[] Returns an array of TipoPrivacidad objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TipoPrivacidad
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
