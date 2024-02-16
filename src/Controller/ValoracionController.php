<?php

namespace App\Controller;

use App\Entity\Comentario;
use App\Entity\Dislike;
use App\Entity\Like;
use App\Entity\Usuario;
use App\Entity\Video;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/valoracion')]
class ValoracionController extends AbstractController
{
    #[Route('/crear', name: 'api_like_create', methods: ['POST'])]
    public function create(NotificacionController $notificacionController,EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data["esLike"] == true){
            $like = new Like();
            $usuario = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $data["usuario"]["id"]]);
            $like->setUsuario($usuario[0]);

            if ($data["video"] == null) {
                $haDadoDisLikePreviamente = $entityManager->getRepository(Dislike::class)
                    ->findBy(["usuario" => $data["usuario"]["id"],"comentario"=>$data["comentario"]["id"]]);
                $haDadoLikePreviamente = $entityManager->getRepository(Like::class)
                    ->findBy(["usuario" => $data["usuario"]["id"],"comentario"=>$data["comentario"]["id"]]);
                if ($haDadoDisLikePreviamente != null){
                    $comentario = $entityManager->getRepository(Comentario::class)->findBy(["id"=> $data["comentario"]["id"]]);
                    $like->setComentario($comentario[0]);
                    $comentario[0]->setContadorLikes($comentario[0]->getContadorLikes() + 1);
                    $comentario[0]->setContadorDislikes($comentario[0]->getContadorDislikes() - 1);
                    $entityManager->persist($like);
                    $entityManager->persist($comentario[0]);
                    $entityManager->remove($haDadoDisLikePreviamente[0]);
                    $entityManager->flush();
                    $lista = [$comentario[0]->getUsuario(),3,"Nuevo like"];
                    $notificacionController->crear($entityManager,$lista);
                }elseif ($haDadoLikePreviamente != null){
                    $comentario = $entityManager->getRepository(Comentario::class)->findBy(["id"=> $data["comentario"]["id"]]);
                    $comentario[0]->setContadorLikes($comentario[0]->getContadorLikes() - 1);
                    $entityManager->persist($comentario[0]);
                    $entityManager->remove($haDadoLikePreviamente[0]);
                    $entityManager->flush();
                }else{
                    $comentario = $entityManager->getRepository(Comentario::class)->findBy(["id"=> $data["comentario"]["id"]]);
                    $like->setComentario($comentario[0]);
                    $comentario[0]->setContadorLikes($comentario[0]->getContadorLikes() + 1);
                    $entityManager->persist($like);
                    $entityManager->persist($comentario[0]);
                    $entityManager->flush();
                    $lista = [$comentario[0]->getUsuario(),3,"Nuevo like"];
                    $notificacionController->crear($entityManager,$lista);
                }

            }elseif ($data["comentario"] == null){
                $haDadoDisLikePreviamente = $entityManager->getRepository(Dislike::class)
                    ->findBy(["usuario" => $data["usuario"]["id"],"video"=>$data["video"]["id"]]);
                $haDadoLikePreviamente = $entityManager->getRepository(Like::class)
                    ->findBy(["usuario" => $data["usuario"]["id"],"video"=>$data["video"]["id"]]);
                if ($haDadoDisLikePreviamente != null){
                    $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]["id"]]);
                    $like->setVideo($video[0]);
                    $video[0]->setContadorLikes($video[0]->getContadorLikes() + 1);
                    $video[0]->setContadorDislikes($video[0]->getContadorDislikes() - 1);
                    $entityManager->persist($like);
                    $entityManager->persist($video[0]);
                    $entityManager->remove($haDadoDisLikePreviamente[0]);
                    $entityManager->flush();
                    $lista = [($video[0]->getCanal())->getUsuario(),3,"Nuevo like"];
                    $notificacionController->crear($entityManager,$lista);
                }elseif ($haDadoLikePreviamente != null){
                    $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]["id"]]);
                    $video[0]->setContadorLikes($video[0]->getContadorLikes() - 1);
                    $entityManager->persist($video[0]);
                    $entityManager->remove($haDadoLikePreviamente[0]);
                    $entityManager->flush();
                }else{
                    $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]["id"]]);
                    $like->setVideo($video[0]);
                    $video[0]->setContadorLikes($video[0]->getContadorLikes() + 1);
                    $entityManager->persist($like);
                    $entityManager->persist($video[0]);
                    $entityManager->flush();
                    $lista = [($video[0]->getCanal())->getUsuario(),3,"Nuevo like"];
                    $notificacionController->crear($entityManager,$lista);
                }
            }

        }elseif ($data["esLike"] == false){
            $disLike = new Dislike();

            $usuario = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $data["usuario"]["id"]]);
            $disLike->setUsuario($usuario[0]);

            if ($data["video"] == null) {
                $haDadoDisLikePreviamente = $entityManager->getRepository(Dislike::class)
                    ->findBy(["usuario" => $data["usuario"]["id"],"comentario"=>$data["comentario"]["id"]]);
                $haDadoLikePreviamente = $entityManager->getRepository(Like::class)
                    ->findBy(["usuario" => $data["usuario"]["id"],"comentario"=>$data["comentario"]["id"]]);
                if ($haDadoDisLikePreviamente != null){
                    $comentario = $entityManager->getRepository(Comentario::class)->findBy(["id"=> $data["comentario"]["id"]]);
                    $comentario[0]->setContadorDislikes($comentario[0]->getContadorDislikes() - 1);
                    $entityManager->persist($comentario[0]);
                    $entityManager->remove($haDadoDisLikePreviamente[0]);
                    $entityManager->flush();
                }elseif ($haDadoLikePreviamente != null){
                    $comentario = $entityManager->getRepository(Comentario::class)->findBy(["id"=> $data["comentario"]["id"]]);
                    $disLike->setComentario($comentario[0]);
                    $comentario[0]->setContadorLikes($comentario[0]->getContadorLikes() - 1);
                    $comentario[0]->setContadorDislikes($comentario[0]->getContadorDislikes() + 1);
                    $entityManager->persist($disLike);
                    $entityManager->persist($comentario[0]);
                    $entityManager->remove($haDadoLikePreviamente[0]);
                    $entityManager->flush();
                    $lista = [$comentario[0]->getUsuario(),4,"Nuevo dislike"];
                    $notificacionController->crear($entityManager,$lista);
                }else{
                    $comentario = $entityManager->getRepository(Comentario::class)->findBy(["id"=> $data["comentario"]["id"]]);
                    $disLike->setComentario($comentario[0]);
                    $comentario[0]->setContadorDislikes($comentario[0]->getContadorDislikes() + 1);
                    $entityManager->persist($disLike);
                    $entityManager->persist($comentario[0]);
                    $entityManager->flush();
                    $lista = [$comentario[0]->getUsuario(),4,"Nuevo dislike"];
                    $notificacionController->crear($entityManager,$lista);
                }

            }elseif ($data["comentario"] == null){
                $haDadoDisLikePreviamente = $entityManager->getRepository(Dislike::class)
                    ->findBy(["usuario" => $data["usuario"]["id"],"video"=>$data["video"]["id"]]);
                $haDadoLikePreviamente = $entityManager->getRepository(Like::class)
                    ->findBy(["usuario" => $data["usuario"]["id"],"video"=>$data["video"]["id"]]);
                if ($haDadoDisLikePreviamente != null){
                    $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]["id"]]);
                    $video[0]->setContadorDislikes($video[0]->getContadorDislikes() - 1);
                    $entityManager->persist($video[0]);
                    $entityManager->remove($haDadoDisLikePreviamente[0]);
                    $entityManager->flush();
                }elseif ($haDadoLikePreviamente != null){
                    $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]["id"]]);
                    $disLike->setVideo($video[0]);
                    $video[0]->setContadorLikes($video[0]->getContadorLikes() - 1);
                    $video[0]->setContadorDislikes($video[0]->getContadorDislikes() + 1);
                    $entityManager->persist($disLike);
                    $entityManager->persist($video[0]);
                    $entityManager->remove($haDadoLikePreviamente[0]);
                    $entityManager->flush();
                    $lista = [($video[0]->getCanal())->getUsuario(),4,"Nuevo dislike"];
                    $notificacionController->crear($entityManager,$lista);
                }else{
                    $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]["id"]]);
                    $disLike->setVideo($video[0]);
                    $video[0]->setContadorDislikes($video[0]->getContadorDislikes() + 1);
                    $entityManager->persist($disLike);
                    $entityManager->persist($video[0]);
                    $entityManager->flush();
                    $lista = [($video[0]->getCanal())->getUsuario(),4,"Nuevo dislike"];
                    $notificacionController->crear($entityManager,$lista);
                }
            }
        }

        return $this->json(['message' => 'Valoración creada'], Response::HTTP_CREATED);

    }

    #[Route('/cargarValoracionesVideo', name: "cargar_valoraciones_video", methods: ["POST"])]
    public function get(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request-> getContent(), true);
        $video = $entityManager->getRepository(Video::class)->findById(['id' => $data["id"]]);

        return $this->json(['likes' => $video[0]->getContadorLikes(), 'dislikes' => $video[0]->getContadorDislikes()]);
    }

}
