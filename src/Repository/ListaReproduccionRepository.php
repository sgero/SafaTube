<?php

namespace App\Repository;

use App\Entity\ListaReproduccion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ListaReproduccion>
 *
 * @method ListaReproduccion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListaReproduccion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListaReproduccion[]    findAll()
 * @method ListaReproduccion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListaReproduccionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListaReproduccion::class);
    }

    public function getListas(array $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idCanal = $id["id"];
        $sql = 'select l.* from safatuber24.lista_reproduccion l join safatuber24.canal c on l.id_canal = c.id where c.id = :idCanal;';
        $resultSet = $conn->executeQuery($sql, ['idCanal' => $idCanal]);
        return $resultSet->fetchAllAssociative();
    }

    public function anyadirVideo(array $datos): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $idLista = $datos["id"]["lista"]["id"];
        $idVideo = $datos["id"]["video"]["id"];
        $sql = 'insert into safatuber24.video_lista_reproduccion (id_lista_reproduccion, id_video) values (:id_lista_reproduccion, :id_video);';
        $resultSet = $conn->executeQuery($sql, ['id_lista_reproduccion' => $idLista, 'id_video' => $idVideo]);
        return $resultSet->fetchAllAssociative();
    }


//    /**
//     * @return ListaReproduccion[] Returns an array of ListaReproduccion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ListaReproduccion
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
