<?php

namespace App\Controller;

use App\Entity\Canal;
use App\Entity\Usuario;
use App\Repository\CanalRepository;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/canal')]
class CanalController extends AbstractController
{

    #[Route('/listar', name: "canal_list", methods: ["GET"])]
    public function list(CanalRepository $canalRepository):JsonResponse
    {
        $list = $canalRepository->findAll();

        return $this->json($list);
    }

    #[Route('/get/{id}', name: "canal_by_id", methods: ["GET"])]
    public function getById(Canal $canal):JsonResponse
    {
        return $this->json($canal);
    }

    #[Route('/crear', name: "crear_canal", methods: ["POST"])]
    public function crear(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $json = json_decode($request-> getContent(), true);

        $nuevoCanal = new Canal();
        $nuevoCanal->setDescripcion($json["descripcion"]);
        $nuevoCanal->setNombre($json["nombre"]);
        $nuevoCanal->setApellidos($json["apellidos"]);
//        $nuevoCanal->setEmail($json["email"]);
        $nuevoCanal->setFechaNacimiento($json["fecha_nacimiento"]);
        $nuevoCanal->setTelefono($json["telefono"]);
        $nuevoCanal->setFoto($json["foto"]);
        $nuevoCanal->setTipoContenido($json["tipo_contenido"]);
        $nuevoCanal->setBanner($json["banner"]);

        $usuario = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $json["usuario"]]);
        $nuevoCanal->setUsuario($usuario[0]);

        $entityManager->persist($nuevoCanal);
        $entityManager->flush();

        return $this->json(['message' => 'Canal creado'], Response::HTTP_CREATED);
    }


    #[Route('/editar/{id}', name: "editar_canal", methods: ["PUT"])]
    public function editar(EntityManagerInterface $entityManager, Request $request, Canal $canal):JsonResponse
    {
        $json = json_decode($request-> getContent(), true);

        $canal = new Canal();
        $canal->setDescripcion($json["descripcion"]);
        $canal->setNombre($json["nombre"]);
        $canal->setApellidos($json["apellidos"]);
//        $canal->setEmail($json["email"]);
        $canal->setFechaNacimiento($json["fecha_nacimiento"]);
        $canal->setTelefono($json["telefono"]);
        $canal->setFoto($json["foto"]);
        $canal->setTipoContenido($json["tipo_contenido"]);
        $canal->setBanner($json["banner"]);

        $usuario = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $json["usuario"]]);
        $canal->setUsuario($usuario[0]);

        $entityManager->flush();

        return $this->json(['message' => 'Canal modificado'], Response::HTTP_OK);
    }

    #[Route('/eliminar/{id}', name: "delete_by_id", methods: ["DELETE"])]
    public function deleteById(EntityManagerInterface $entityManager, Canal $canal):JsonResponse
    {
        $entityManager->remove($canal);
        $entityManager->flush();

        return $this->json(['message' => 'Canal eliminado'], Response::HTTP_OK);
    }




}
