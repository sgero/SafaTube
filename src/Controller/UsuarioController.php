<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/usuario')]
class UsuarioController extends AbstractController
{

    #[Route('', name: "usuario_list", methods: ["GET"])]
    public function list(UsuarioRepository $usuarioRepository):JsonResponse
    {
        $list = $usuarioRepository->findAll();

        return $this->json($list);
    }

    #[Route('/{id}', name: "clase_by_id", methods: ["GET"])]
    public function getById(Usuario $usuario):JsonResponse
    {
        return $this->json($usuario);

    }

    #[Route('', name: "crear_usuario", methods: ["POST"])]
    public function crear(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $json = json_decode($request-> getContent(), true);
        $nuevoUsuario = new Usuario();
        $nuevoUsuario->setUsername($json["username"]);
        $nuevoUsuario->setPassword($json["password"]);


        $entityManager->persist($nuevoUsuario);
        $entityManager->flush();

        return $this->json(['message' => 'Usuario creado'], Response::HTTP_CREATED);
    }


    #[Route('/{id}', name: "editar_usuario", methods: ["PUT"])]
    public function editar(EntityManagerInterface $entityManager, Request $request, Usuario $usuario):JsonResponse
    {
        $json = json_decode($request-> getContent(), true);

        $usuario->setUsername($json["username"]);
        $usuario->setPassword($json["password"]);

        $entityManager->flush();

        return $this->json(['message' => 'Usuario modificado'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: "delete_by_id", methods: ["DELETE"])]
    public function deleteById(EntityManagerInterface $entityManager, Usuario $usuario):JsonResponse
    {
        $entityManager->remove($usuario);
        $entityManager->flush();

        return $this->json(['message' => 'Usuario eliminado'], Response::HTTP_OK);
    }




}
