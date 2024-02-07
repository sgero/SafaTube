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
    public function create(EntityManagerInterface $entityManager, Request $request): JsonResponse
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
                    $entityManager->remove($haDadoDisLikePreviamente);
                    $entityManager->flush();
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
                }






            }elseif ($data["comentario"] == null){
                $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]["id"]]);
                $like->setVideo($video[0]);
                $video[0]->setContadorLikes($video[0]->getContadorLikes() + 1);
                $entityManager->persist($like);
                $entityManager->persist($video[0]);
                $entityManager->flush();
            }









        }elseif ($data["esLike"] == false){
            $disLike = new Dislike();

            $usuario = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $data["usuario"]["id"]]);
            $disLike->setUsuario($usuario[0]);

            if ($data["video"] == null) {
                $comentario = $entityManager->getRepository(Comentario::class)->findBy(["id"=> $data["comentario"]["id"]]);
                $disLike->setComentario($comentario[0]);
                $comentario->setContadorLikes($comentario->getContadorLikes() + 1);
                $entityManager->persist($disLike);
                $entityManager->persist($comentario);
                $entityManager->flush();
            }elseif ($data["comentario"] == null){
                $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]["id"]]);
                $disLike->setVideo($video[0]);
                $entityManager->persist($disLike);
                $entityManager->flush();
            }
        }

        return $this->json(['message' => 'Valoracion creada'], Response::HTTP_CREATED);


    }


}
