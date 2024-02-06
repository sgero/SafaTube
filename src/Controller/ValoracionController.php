<?php

namespace App\Controller;

use App\Entity\Comentario;
use App\Entity\Like;
use App\Entity\Usuario;
use App\Entity\Video;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $like = new Like();

        $usuario = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $data["usuario"]]);
        $like->setUsuario($usuario[0]);

        if ($data["video"] == null) {
            $comentario = $entityManager->getRepository(Comentario::class)->findBy(["id"=> $data["comentario"]]);
            $like->setComentario($comentario[0]);
            $entityManager->persist($like);
            $entityManager->flush();
        }elseif ($data["comentario"] == null){
            $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]]);
            $like->setVideo($video[0]);
            $entityManager->persist($like);
            $entityManager->flush();
        }

        return $this->json(['message' => 'Like creado'], Response::HTTP_CREATED);
    }


}
