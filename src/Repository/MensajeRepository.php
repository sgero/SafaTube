<?php

namespace App\Repository;

use App\Entity\Mensaje;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mensaje>
 *
 * @method Mensaje|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mensaje|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mensaje[]    findAll()
 * @method Mensaje[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MensajeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mensaje::class);
    }
    public function getMensajes(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idUsuario = $id["id"];
        $idConversor = $id["id2"];
        $sql = 'select m.* from safatuber24.mensaje m where (m.id_usuario_emisor = :id 
                                       and m.id_usuario_receptor = :id2) 
                                       or (m.id_usuario_emisor = :id2
                                       and m.id_usuario_receptor = :id)';
        $resultSet = $conn->executeQuery($sql, ['id' => $idUsuario,
            //'id_usuario_receptor' => $idUsuario,
            //'id_usuario_emisor' => $idConversor,
            'id2' => $idConversor]);
        return $resultSet->fetchAllAssociative();
    }
    public function getBusqueda(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idUsuario = $id["id"];
        $sql = 'select distinct u.* from safatuber24.usuario u join safatuber24.mensaje m on u.id = m.id_usuario_emisor or u.id = m.id_usuario_receptor where (m.id_usuario_emisor = :id
                                       and m.id_usuario_receptor = u.id)
                                       or (m.id_usuario_emisor = u.id
                                       and m.id_usuario_receptor = :id)';
        $resultSet = $conn->executeQuery($sql, ['id' => $idUsuario]);
        return $resultSet->fetchAllAssociative();
    }
//    /**
//     * @return Mensaje[] Returns an array of Mensaje objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Mensaje
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
