<?php

namespace App\Controller;

use App\Entity\Suscripcion;
use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/usuario')]
class UsuarioController extends AbstractController
{

    #[Route('/listar', name: "usuario_list", methods: ["GET"])]
    public function list(UsuarioRepository $usuarioRepository):JsonResponse
    {
        $list = $usuarioRepository->findAll();

        return $this->json($list);
    }

    #[Route('/get', name: "usuario_log", methods: ["POST"])]
    public function get(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request-> getContent(), true);
        $usuarioData = $entityManager->getRepository(Usuario::class)->getByUsername($data);
        $usuarioLogeado = $entityManager->getRepository(Usuario::class)->find($usuarioData[0]["id"]);

        return $this->json($usuarioLogeado);
    }

    #[Route('/get/{id}', name: "usuario_by_id", methods: ["GET"])]
    public function getById(Usuario $usuario):JsonResponse
    {
        return $this->json($usuario);
    }

    #[Route('/crear', name: "crear_usuario", methods: ["POST"])]
    public function crear(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher):JsonResponse
    {
        $json = json_decode($request-> getContent(), true);
        $nuevoUsuario = new Usuario();
        $nuevoUsuario->setUsername($json["username"]);
        $nuevoUsuario->setPassword($passwordHasher->hashPassword($nuevoUsuario,$json["password"]));

        $entityManager->persist($nuevoUsuario);
        $entityManager->flush();

        return $this->json(['message' => 'Usuario creado'], Response::HTTP_CREATED);
    }

    #[Route('/editar/{id}', name: "editar_usuario", methods: ["PUT"])]
    public function editar(EntityManagerInterface $entityManager, Request $request, Usuario $usuario):JsonResponse
    {
        $json = json_decode($request-> getContent(), true);

        $usuario->setUsername($json["username"]);
        $usuario->setPassword($json["password"]);

        $entityManager->flush();

        return $this->json(['message' => 'Usuario modificado'], Response::HTTP_OK);
    }

    #[Route('/eliminar/{id}', name: "delete_by_id", methods: ["DELETE"])]
    public function deleteById(EntityManagerInterface $entityManager, Usuario $usuario):JsonResponse
    {
        $entityManager->remove($usuario);
        $entityManager->flush();

        return $this->json(['message' => 'Usuario eliminado'], Response::HTTP_OK);
    }


    //El método de LOGIN lo implementa el SECURITY BUNDLE
//    #[Route('/login', name: "login", methods: ["POST"])]
//    public function login(Request $request, UsuarioRepository $usuarioRepository, UserPasswordHasherInterface $passwordHasher):JsonResponse
//    {
//        $json = json_decode($request-> getContent(), true);
//
//        $usuario = $usuarioRepository->findOneBy(["username" => $json["username"]]);
//
//        if(!$usuario){
//            return $this->json(['message' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
//        }
//
//        if(!$passwordHasher->isPasswordValid($usuario, $json["password"])){
//            return $this->json(['message' => 'Contraseña incorrecta'], Response::HTTP_BAD_REQUEST);
//        }
//
//        return $this->json(['message' => 'Bienvenido'], Response::HTTP_OK);
//    }


}
