<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Video>
 *
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }


    public function findVideosPorCanal(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idCanal = $id["id"];
        $sql = '
            SELECT v.* FROM safatuber24.video v join safatuber24.canal c on v.id_canal = c.id 
            WHERE c.id = :id            ';

        $resultSet = $conn->executeQuery($sql, ['id' => $idCanal]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function findVideosPorCategoria(array $nombre): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $tipoCategoria = $nombre["nombre"];
        $sql = '
            SELECT v.* FROM safatuber24.video v join safatuber24.tipo_categoria tc on v.id_tipo_categoria = tc.id 
            WHERE tc.nombre ILIKE :nombre            ';

        $resultSet = $conn->executeQuery($sql, ['nombre' => $tipoCategoria]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function findVideos(array $titulo): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $video = '%' . $titulo["titulo"] . '%';
        $sql = '
            SELECT v.* FROM safatuber24.video v 
            WHERE v.titulo ILIKE :titulo         ';

        $resultSet = $conn->executeQuery($sql, ['titulo' => $video]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

//    /**
//     * @return Video[] Returns an array of Video objects
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

//    public function findOneBySomeField($value): ?Video
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
