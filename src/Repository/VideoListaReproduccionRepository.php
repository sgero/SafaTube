<?php

namespace App\Repository;

use App\Entity\VideoListaReproduccion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VideoListaReproduccion>
 *
 * @method VideoListaReproduccion|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideoListaReproduccion|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideoListaReproduccion[]    findAll()
 * @method VideoListaReproduccion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoListaReproduccionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoListaReproduccion::class);
    }

//    /**
//     * @return VideoListaReproduccion[] Returns an array of VideoListaReproduccion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VideoListaReproduccion
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
