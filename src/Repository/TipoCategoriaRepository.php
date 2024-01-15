<?php

namespace App\Repository;

use App\Entity\TipoCategoria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipoCategoria>
 *
 * @method TipoCategoria|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoCategoria|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoCategoria[]    findAll()
 * @method TipoCategoria[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoCategoriaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoCategoria::class);
    }

//    /**
//     * @return TipoCategoria[] Returns an array of TipoCategoria objects
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

//    public function findOneBySomeField($value): ?TipoCategoria
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
