<?php

namespace App\Controller;

use App\Entity\Canal;
use App\Entity\Suscripcion;
use App\Entity\Usuario;
use App\Entity\Video;
use App\Repository\SuscripcionRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/suscripcion')]
class SuscripcionController extends AbstractController
{
    #[Route('/listar', name: 'listar_suscripcion', methods: ['GET'])]
    public function list(SuscripcionRepository $suscripcionRepository): JsonResponse
    {
        $suscripciones = $suscripcionRepository->findAll();

        return $this->json($suscripciones);
    }

    #[Route('/get/{id}', name: 'suscripcion_by_id', methods: ['GET'])]
    public function getById(Suscripcion $suscripcion): JsonResponse
    {
        return $this->json($suscripcion);
    }

    #[Route('/crear', name: 'crear_suscripcion', methods: ['POST'])]
    public function crear(EntityManagerInterface $entityManager, Request $request,SuscripcionRepository $suscripcionRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $nuevaSuscripcion= new Suscripcion();

        $usuario = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $data["usuario_suscriptor"]]);
        $nuevaSuscripcion->setIdUsuarioSuscriptor($usuario[0]);

        $canal = $entityManager->getRepository(Canal::class)->findBy(["id"=> $data["canal_suscrito"]]);
        $nuevaSuscripcion->setIdCanalSuscrito($canal[0]);

        $entityManager->persist($nuevaSuscripcion);
        $entityManager->flush();

        return $this->json(['message' => 'Suscripción creada correctamente'], Response::HTTP_CREATED);
    }

    #[Route('/editar/{id}', name: "editar_suscripcion", methods: ["PUT"])]
    public function editar(EntityManagerInterface $entityManager, Request $request, Suscripcion $suscripcion):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $usuario = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $data["usuario_suscriptor"]]);
        $suscripcion->setIdUsuarioSuscriptor($usuario[0]);

        $canal = $entityManager->getRepository(Canal::class)->findBy(["id"=> $data["canal_suscrito"]]);
        $suscripcion->setIdCanalSuscrito($canal[0]);

        $entityManager->flush();

        return $this->json(['message' => 'Suscripción modificada'], Response::HTTP_OK);
    }

    #[Route('/eliminar/{id}', name: "borrar_suscripcion", methods: ["DELETE"])]
    public function deleteById(EntityManagerInterface $entityManager, Suscripcion $suscripcion):JsonResponse
    {

        $entityManager->remove($suscripcion);
        $entityManager->flush();

        return $this->json(['message' => 'Suscripción eliminada'], Response::HTTP_OK);

    }
}
