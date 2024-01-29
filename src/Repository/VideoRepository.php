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

    public function getVideosRecomendados(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idTipoCategoria = $id["id"];
        $sql = 'select * from video v join safatuber24.tipo_categoria tc on v.id_tipo_categoria = tc.id 
         where v.id_tipo_categoria = :id order by v.fecha desc limit 5';
        $resultSet = $conn->executeQuery($sql, ['id' => $idTipoCategoria]);
        return $resultSet->fetchAllAssociative();
    }

    public function getVideosCanalesSuscritos(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idUsuarioSuscriptor = $id["id"];
        $sql = 'select v.* from safatuber24.suscripcion s 
                join safatuber24.usuario u on s.id_usuario_suscriptor = u.id
                join safatuber24.canal c on s.id_canal_suscrito = c.id
                join safatuber24.video v on c.id = v.id_canal
                where s.id_usuario_suscriptor = :id 
                order by v.fecha desc';
        $resultSet = $conn->executeQuery($sql, ['id' => $idUsuarioSuscriptor]);
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
