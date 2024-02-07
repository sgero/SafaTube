<?php

namespace App\Controller;

use App\Entity\Canal;
use App\Entity\Comentario;
use App\Entity\Suscripcion;
use App\Entity\Usuario;
use App\Entity\Video;
use App\Repository\ComentarioRepository;
use App\Repository\SuscripcionRepository;
use \DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/comentario')]
class ComentarioController extends AbstractController
{
    #[Route('/listar', name: 'listar_comentario', methods: ['GET'])]
    public function list(ComentarioRepository $comentarioRepository): JsonResponse
    {
        $comentarios = $comentarioRepository->findAll();

        return $this->json($comentarios);
    }

    #[Route('/get/{id}', name: 'comentario_by_id', methods: ['GET'])]
    public function getById(Comentario $comentario): JsonResponse
    {
        return $this->json($comentario);
    }

    #[Route('/crear', name: 'crear_comentario', methods: ['POST'])]
    public function crear(EntityManagerInterface $entityManager, Request $request,ComentarioRepository $comentarioRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $nuevoComentario = new Comentario();

        $nuevoComentario->setTexto($data['texto']);
        $nuevoComentario->setFecha(date('Y-m-d H:i:s'));
        $nuevoComentario->setContadorLikes(0);
        $nuevoComentario->setContadorDislikes(0);

        $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]["id"]]);
        $nuevoComentario->setIdVideo($video[0]);

        $usuario = $entityManager->getRepository(Usuario::class)->findBy(["id"=> 12]);
        $nuevoComentario->setUsuario($usuario[0]);

        if ($data["comentarioPadre"] == null){
            $entityManager->persist($nuevoComentario);
            $entityManager->flush();
        }else {
            $comentarioPadre = $entityManager->getRepository(Comentario::class)->findBy(["id"=> $data["comentarioPadre"]["id"]]);
            $nuevoComentario->setComentarioPadre($comentarioPadre[0]);
            $usuarioMencionado = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $data["usuarioMencionado"]]);
            $nuevoComentario->setUsuarioMencionado($usuarioMencionado[0]);
            $entityManager->persist($nuevoComentario);
            $entityManager->flush();
        }

        return $this->json(['message' => 'Comentario creado correctamente'], Response::HTTP_CREATED);
    }

    #[Route('/editar/{id}', name: "editar_comentario", methods: ["PUT"])]
    public function editar(EntityManagerInterface $entityManager, Request $request, Comentario $comentario):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $comentario->setTexto($data['texto']);
        $comentario->setFecha(date('Y-m-d H:i:s'));

        $entityManager->flush();

        return $this->json(['message' => 'Comentario modificado'], Response::HTTP_OK);
    }

    #[Route('/eliminar/{id}', name: "borrar_comentario", methods: ["DELETE"])]
    public function deleteById(EntityManagerInterface $entityManager, Comentario $comentario):JsonResponse
    {
        $entityManager->remove($comentario);
        $entityManager->flush();

        return $this->json(['message' => 'Comentario eliminado'], Response::HTTP_OK);

    }
}
