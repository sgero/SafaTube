<?php

namespace App\Repository;

use App\Entity\TipoContenido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipoContenido>
 *
 * @method TipoContenido|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoContenido|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoContenido[]    findAll()
 * @method TipoContenido[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoContenidoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoContenido::class);
    }

//    /**
//     * @return TipoContenido[] Returns an array of TipoContenido objects
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

//    public function findOneBySomeField($value): ?TipoContenido
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
