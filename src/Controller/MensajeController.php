<?php

namespace App\Controller;

use App\Entity\Mensaje;
use App\Entity\Usuario;
use App\Repository\MensajeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use \DateTime;

#[Route('/api/mensaje')]
class MensajeController extends AbstractController
{
    #[Route('/listar', name: 'api_mensaje_list', methods: ['GET'])]
    public function list(MensajeRepository $mensajeRepository): JsonResponse
    {
        $mensajes = $mensajeRepository->findAll();
    
        return $this->json($mensajes);
    }

    #[Route('/get/{id}', name: 'api_mensaje_show', methods: ['GET'])]
    public function show(Mensaje $mensaje): JsonResponse
    {
        return $this->json($mensaje);
    }

    #[Route('/crear', name: 'api_mensaje_create', methods: ['POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $mensaje = new Mensaje();
        $mensaje->setTexto($data['texto']);
        $mensaje->setFecha(new DateTime());

        $usuarioemisor = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $data["usuario_emisor"]]);
        $mensaje->setUsuarioEmisor($usuarioemisor[0]);
        $usuarioreceptor = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $data["usuario_receptor"]]);
        $mensaje->setUsuarioReceptor($usuarioreceptor[0]);


        $entityManager->persist($mensaje);
        $entityManager->flush();

        return $this->json(['message' => 'Mensaje creado'], Response::HTTP_CREATED);
    }

    #[Route('/editar/{id}', name: 'api_mensaje_update', methods: ['PUT'])]
    public function update(EntityManagerInterface $entityManager, Request $request, Mensaje $mensaje): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $mensaje->setTexto($data['texto']);
        $mensaje->setFecha(new DateTime());


        $entityManager->flush();

        return $this->json(['message' => 'Mensaje actualizado']);
    }

    #[Route('/eliminar/{id}', name: "api_delete_by_id", methods: ["DELETE"])]
    public function deleteById(EntityManagerInterface $entityManager, Mensaje $mensaje):JsonResponse
    {

        $entityManager->remove($mensaje);
        $entityManager->flush();

        return $this->json(['message' => 'Mensaje eliminado'], Response::HTTP_OK);

    }

}