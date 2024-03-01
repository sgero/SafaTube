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

    public function getPlayVideo(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idVideo = $id["id"];
        $sql = 'select v.*, c.foto as foto_canal from safatuber24.video v join safatuber24.canal c2 on c2.id = v.id_canal where v.id = :id and v.activo = true;';
        $resultSet = $conn->executeQuery($sql, ['id' => $idVideo]);
        return $resultSet->fetchAllAssociative();
    }

    public function findVideosPorCanal(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idCanal = $id["id"];
        $sql = '
            SELECT v.* FROM safatuber24.video v join safatuber24.canal c on v.id_canal = c.id 
            WHERE c.id = :id and v.activo = true           ';

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
            WHERE tc.nombre ILIKE :nombre  and v.activo = true          ';

        $resultSet = $conn->executeQuery($sql, ["nombre" => $tipoCategoria]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function findVideos(array $titulo): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $video = '%' . $titulo["titulo"] . '%';
        $sql = '
            SELECT v.*, c.nombre, c.foto FROM safatuber24.video v join safatuber24.canal c on c.id = v.id_canal
            WHERE v.titulo ILIKE :titulo   and v.id_tipo_privacidad = 1  and v.activo = true    ';

        $resultSet = $conn->executeQuery($sql, ['titulo' => $video]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function getVideosRecomendadosAPartirDeVideo(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idVideo = $id["id"]["id"];
        $idTipoCategoria = $id["id"]["tipoCategoria"]["id"];
        $sql = 'select v.*, c.nombre as nombre_canal, c.foto as foto_canal from safatuber24.video v
        join safatuber24.tipo_categoria tc on v.id_tipo_categoria = tc.id
        join safatuber24.canal c on c.id = v.id_canal
        where tc.id = :idTipoCategoria and v.id != :idVideo and v.activo = true order by v.fecha desc limit 10;';
        $resultSet = $conn->executeQuery($sql, ['idTipoCategoria' => $idTipoCategoria, 'idVideo' => $idVideo] );
        return $resultSet->fetchAllAssociative();
    }

    public function getVideosRecomendados(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idUsuario = $id["id"];
        $sql = 'select v.*, c.nombre as nombre_canal, c.foto as foto_canal from safatuber24.video v
                join safatuber24.canal c on v.id_canal = c.id
                join safatuber24.tipo_privacidad tp on v.id_tipo_privacidad = tp.id
                where tp.id = 1 and v.activo = true
                order by v.total_visitas desc, v.fecha desc;';
        $resultSet = $conn->executeQuery($sql, ['id' => $idUsuario]);
        return $resultSet->fetchAllAssociative();
    }
    public function getHistorial(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idUsuario = $id["id"]["id"];
        $sql = 'select distinct v.*, c.nombre as nombre_canal, c.foto as foto_canal from safatuber24.visualizacion_video_usuario vvu
                join safatuber24.video v on vvu.id_video = v.id
                join safatuber24.canal c on v.id_canal = c.id
                join safatuber24.usuario u on vvu.id_usuario = u.id
                where u.id = :id and v.activo = true and v.id_tipo_privacidad = 1;';
        $resultSet = $conn->executeQuery($sql, ['id' => $idUsuario]);
        return $resultSet->fetchAllAssociative();
    }


    public function getVideosCanalesSuscritos(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idUsuarioSuscriptor = $id["id"];
        $sql = 'select v.*,  c.nombre as nombre_canal, c.foto as foto_canal from safatuber24.suscripcion s
                join safatuber24.usuario u on s.id_usuario_suscriptor = u.id
                join safatuber24.canal c on s.id_canal_suscrito = c.id
                join safatuber24.video v on c.id = v.id_canal
                join safatuber24.tipo_privacidad tp on v.id_tipo_privacidad = tp.id
                where s.id_usuario_suscriptor = :id and tp.id = 1 and v.activo = true
                order by v.fecha desc;';
        $resultSet = $conn->executeQuery($sql, ['id' => $idUsuarioSuscriptor]);
        return $resultSet->fetchAllAssociative();
    }

    public function getComentariosLista(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idVideo = $id["id"];
        $sql = 'select c.*, c2.nombre as nombre_canal, c2.foto as foto_canal from safatuber24.comentario c
                join safatuber24.usuario u on c.id_usuario = u.id
                left join safatuber24.canal c2 on u.id = c2.id_usuario
                where c.id_video = :id and c.id_comentario_padre IS NULL order by c.fecha;';
        $resultSet = $conn->executeQuery($sql, ['id' => $idVideo]);
        return $resultSet->fetchAllAssociative();
    }

    public function getRespuestaComentariosLista(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idVideo = $id["id"];
        $sql = 'select c.*, c2.nombre as nombre_canal, c2.foto as foto_canal, c3.nombre as nombre_usuario_mencionado
                from safatuber24.comentario c
                join safatuber24.usuario u on c.id_usuario = u.id
                left join safatuber24.canal c2 on u.id = c2.id_usuario
                join safatuber24.usuario u2 on c.id_usuario_mencionado = u2.id
                join safatuber24.canal c3 on u2.id = c3.id_usuario
                where c.id_comentario_padre = :id
                order by c.fecha;';
        $resultSet = $conn->executeQuery($sql, ['id' => $idVideo]);
        return $resultSet->fetchAllAssociative();
    }

    public function anyadirVisita(array $datos): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idUsuario = $datos["id"]["usuario"];
        $idVideo = $datos["id"]["video"];
        $sql = 'insert into safatuber24.visualizacion_video_usuario  (id_usuario, id_video) values (:idUsuario,:idVideo);';
        $resultSet = $conn->executeQuery($sql, ['idUsuario' => $idUsuario, 'idVideo' => $idVideo]);
        return $resultSet->fetchAllAssociative();
    }

    public function getVisitas(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idVideo = $id["id"];
        $sql = 'select count(vvu.id_video) from safatuber24.visualizacion_video_usuario vvu where vvu.id_video = :idVideo group by vvu.id_video;';
        $resultSet = $conn->executeQuery($sql, ['idVideo' => $idVideo]);
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
