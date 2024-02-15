<?php

namespace App\Repository;

use App\Entity\Suscripcion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Suscripcion>
 *
 * @method Suscripcion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Suscripcion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Suscripcion[]    findAll()
 * @method Suscripcion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuscripcionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Suscripcion::class);
    }

    public function verificarSuscripcion(array $datos): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idUsuario = $datos["id"]["usuario"];
        $idCanal = $datos["id"]["canal"];
        $sql = 'select * from safatuber24.suscripcion s
                where s.id_usuario_suscriptor = :idUsuario and s.id_canal_suscrito = :idCanal;';
        $resultSet = $conn->executeQuery($sql, ['idUsuario' => $idUsuario, 'idCanal' => $idCanal]);
        return $resultSet->fetchAllAssociative();
    }
    public function verSuscriptoresEntreDosFechas(array $datos): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idCanal = $datos[0]["idCanal"];
        $fechaInicio = substr($datos[0]["inicio"], 0, 10);
        $fechaFin = substr($datos[0]["fin"], 0, 10);
        $sql = 'select s.* from safatuber24.suscripcion s
                join safatuber24.canal c on s.id_canal_suscrito = c.id
                where c.id = :idCanal and (s.fecha >= :inicio and s.fecha <= :fin);';
        $resultSet = $conn->executeQuery($sql, ['idCanal' => $idCanal, 'inicio' => $fechaInicio, 'fin' => $fechaFin]);
        return $resultSet->fetchAllAssociative();
    }

//    /**
//     * @return Suscripcion[] Returns an array of Suscripcion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Suscripcion
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
