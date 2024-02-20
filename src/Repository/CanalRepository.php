<?php

namespace App\Repository;

use App\Entity\Canal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\PseudoTypes\Numeric_;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @extends ServiceEntityRepository<Canal>
 *
 * @method Canal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Canal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Canal[]    findAll()
 * @method Canal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CanalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Canal::class);
    }

    public function getSubs(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'select distinct u.username, c.id, c.nombre, c.foto from safatuber24.usuario u join safatuber24.canal c
                on u.id = c.id_usuario join safatuber24.suscripcion s 
                on c.id = s.id_canal_suscrito where s.id_usuario_suscriptor = :id';
        $resultSet = $conn->executeQuery($sql, ['id' => $id]);

        return $resultSet->fetchAllAssociative();
    }

    public function canalMensaje(array $id): array
    {
        $sql = 'select distinct c.* from safatuber24.canal c where c.id_usuario in (:id) ';

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Canal::class, 'c');
        $query = $this->getEntityManager()->createNativeQuery($sql,$rsm);
        $query->setParameter("id", $id, ArrayParameterType::INTEGER);

        $result = $query->getResult();
        return $result;
    }

    public function findCanales(array $nombre): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $canal = '%' . $nombre["nombre"] . '%';
        $sql = '
            SELECT c.* FROM safatuber24.canal c 
            WHERE c.nombre ILIKE :nombre         ';

        $resultSet = $conn->executeQuery($sql, ['nombre' => $canal]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function getByUsuarioLogueado(int $usuario): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'select c.* from safatuber24.canal c
                join safatuber24.usuario u on u.id = c.id_usuario where u.id = :id';
        $resultSet = $conn->executeQuery($sql, ['id' => $usuario]);
        return $resultSet->fetchAllAssociative();
    }

    public function getVideosSegunCanal(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idCanal = $id["id"];
        $sql = 'select v.*, c.nombre as nombre_canal, c.foto as foto_canal from safatuber24.canal c
                join safatuber24.video v on c.id = v.id_canal
                where c.id = :id order by v.fecha desc;';
        $resultSet = $conn->executeQuery($sql, ['id' => $idCanal] );
        return $resultSet->fetchAllAssociative();
    }
    public function getVideosPopularesSegunCanal(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idCanal = $id["id"];
        $sql = 'select v.*, c.nombre as nombre_canal, c.foto as foto_canal from safatuber24.canal c
                join safatuber24.video v on c.id = v.id_canal
                where c.id = :id order by v.total_visitas desc;';
        $resultSet = $conn->executeQuery($sql, ['id' => $idCanal] );
        return $resultSet->fetchAllAssociative();
    }

    public function getNumeroVideosSubidos(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idCanal = $id["id"];
        $sql = 'select count(v.id) from safatuber24.video v join safatuber24.canal c on v.id_canal = c.id
                where c.id = :id;';
        $resultSet = $conn->executeQuery($sql, ['id' => $idCanal] );
        return $resultSet->fetchAllAssociative();
    }

    public function getSuscripcionesTotales(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idCanal = $id["id"];
        $sql = 'select count(c.id) from safatuber24.suscripcion s 
                join safatuber24.canal c on c.id = s.id_canal_suscrito
                where c.id = :id;';
        $resultSet = $conn->executeQuery($sql, ['id' => $idCanal] );
        return $resultSet->fetchAllAssociative();
    }

    public function getVisitasTotales(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idCanal = $id["id"];
        $sql = 'select count(vvu) from safatuber24.visualizacion_video_usuario vvu
                join safatuber24.video v on vvu.id_video = v.id
                join safatuber24.canal c on v.id_canal = c.id
                where c.id = :id;';
        $resultSet = $conn->executeQuery($sql, ['id' => $idCanal] );
        return $resultSet->fetchAllAssociative();
    }

//    /**
//     * @return Canal[] Returns an array of Canal objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Canal
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
