<?php

namespace App\Controller;

use App\Entity\Comentario;
use App\Entity\Dislike;
use App\Entity\Usuario;
use App\Entity\Video;
use App\Repository\DislikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/dislikes')]
class DislikeController extends AbstractController
{
    #[Route('/listar', name: 'api_dislike_list', methods: ['GET'])]
    public function list(DislikeRepository $dislikeRepository): JsonResponse
    {
        $dislike = $dislikeRepository->findAll();

        return $this->json($dislike);
    }

    #[Route('/get/{id}', name: 'api_dislike_show', methods: ['GET'])]
    public function show(Dislike $dislike): JsonResponse
    {
        return $this->json($dislike);
    }

    #[Route('/crear', name: 'api_dislike_create', methods: ['POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dislike = new Dislike();

        $usuario = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $data["usuario"]]);
        $dislike->setUsuario($usuario[0]);
        $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]]);
        $dislike->setVideo($video[0]);
        $comentario = $entityManager->getRepository(Comentario::class)->findBy(["id"=> $data["comentario"]]);
        $dislike->setComentario($comentario[0]);


        $entityManager->persist($dislike);
        $entityManager->flush();

        return $this->json(['message' => 'Dislike creado'], Response::HTTP_CREATED);
    }

    #[Route('/editar/{id}', name: 'api_like_update', methods: ['PUT'])]
    public function update(EntityManagerInterface $entityManager, Request $request, Dislike $dislike): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $usuario = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $data["usuario"]]);
        $dislike->setUsuario($usuario[0]);
        $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]]);
        $dislike->setVideo($video[0]);
        $comentario = $entityManager->getRepository(Comentario::class)->findBy(["id"=> $data["comentario"]]);
        $dislike->setComentario($comentario[0]);

        $entityManager->flush();

        return $this->json(['message' => 'Dislike actualizado']);
    }

    #[Route('/eliminar/{id}', name: "delete_by_id", methods: ["DELETE"])]
    public function deleteById(EntityManagerInterface $entityManager, Dislike $dislike):JsonResponse
    {

        $entityManager->remove($dislike);
        $entityManager->flush();

        return $this->json(['message' => 'Dislike eliminado'], Response::HTTP_OK);

    }
}
