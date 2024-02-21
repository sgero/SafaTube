<?php

namespace App\Repository;

use App\Entity\Notificacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notificacion>
 *
 * @method Notificacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notificacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notificacion[]    findAll()
 * @method Notificacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notificacion::class);
    }
    public function getcountmensaje(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'select count(n.id) from safatuber24.notificacion n 
                   where n.id_canal = :id and n.id_tipo_notificacion = 5 
                     and n.atendida = false';
        $resultSet = $conn->executeQuery($sql, ['id' => $id]);

        return $resultSet->fetchAllAssociative();
    }

    public function getcountlike(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'select count(n.id) from safatuber24.notificacion n 
                   where n.id_canal = :id and n.id_tipo_notificacion = 3 
                     and n.atendida = false';
        $resultSet = $conn->executeQuery($sql, ['id' => $id]);

        return $resultSet->fetchAllAssociative();
    }

    public function getcountDislike(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'select count(n.id) from safatuber24.notificacion n 
                   where n.id_canal = :id and n.id_tipo_notificacion = 4 
                     and n.atendida = false';
        $resultSet = $conn->executeQuery($sql, ['id' => $id]);

        return $resultSet->fetchAllAssociative();
    }

    public function getcountSubs(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'select count(n.id) from safatuber24.notificacion n 
                   where n.id_canal = :id and n.id_tipo_notificacion = 1 
                     and n.atendida = false';
        $resultSet = $conn->executeQuery($sql, ['id' => $id]);

        return $resultSet->fetchAllAssociative();
    }

    public function getNotiSuscripciones(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'select n.id from safatuber24.notificacion n 
                   where n.id_canal = :id and n.id_tipo_notificacion = 2 
                     and n.atendida = false';
        $resultSet = $conn->executeQuery($sql, ['id' => $id]);

        return $resultSet->fetchAllAssociative();
    }

//    /**
//     * @return Notificacion[] Returns an array of Notificacion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Notificacion
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
