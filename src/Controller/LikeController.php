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

#[Route('/api/likes')]
class LikeController extends AbstractController
{
    #[Route('/listar', name: 'api_like_list', methods: ['GET'])]
    public function list(LikeRepository $likeRepository): JsonResponse
    {
        $like = $likeRepository->findAll();

        return $this->json($like);
    }

    #[Route('/get/{id}', name: 'api_like_show', methods: ['GET'])]
    public function show(Like $like): JsonResponse
    {
        return $this->json($like);
    }

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

    #[Route('/editar/{id}', name: 'api_like_update', methods: ['PUT'])]
    public function update(EntityManagerInterface $entityManager, Request $request, Like $like): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $usuario = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $data["usuario"]]);
        $like->setUsuario($usuario[0]);
        $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]]);
        $like->setVideo($video[0]);
        $comentario = $entityManager->getRepository(Comentario::class)->findBy(["id"=> $data["comentario"]]);
        $like->setComentario($comentario[0]);

        $entityManager->flush();

        return $this->json(['message' => 'Like actualizado']);
    }

    #[Route('/eliminar/{id}', name: "delete_by_id", methods: ["DELETE"])]
    public function deleteById(EntityManagerInterface $entityManager, Like $like):JsonResponse
    {

        $entityManager->remove($like);
        $entityManager->flush();

        return $this->json(['message' => 'Like eliminado'], Response::HTTP_OK);

    }
}
