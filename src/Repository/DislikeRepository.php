<?php

namespace App\Repository;

use App\Entity\Dislike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dislike>
 *
 * @method Dislike|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dislike|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dislike[]    findAll()
 * @method Dislike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DislikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dislike::class);
    }

    public function estadisticasValoracionesVideoDislikes(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idCanal = $id["id"];
        $sql = 'select count(l.id) from safatuber24.dislikes l
                join safatuber24.video v on l.id_video = v.id
                join safatuber24.canal c on v.id_canal = c.id
                where c.id = :id;';
        $resultSet = $conn->executeQuery($sql, ['id' => $idCanal]);
        return $resultSet->fetchAllAssociative();
    }

//    /**
//     * @return Dislike[] Returns an array of Dislike objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Dislike
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
