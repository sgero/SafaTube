<?php

namespace App\Controller;

use App\Entity\Mensaje;
use App\Entity\Usuario;
use App\Repository\CanalRepository;
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
    #[Route('/listartodo', name: 'api_mensaje_listtodo', methods: ['GET'])]
    public function listtodo(MensajeRepository $mensajeRepository): JsonResponse
    { //añadir esto a los variables que entra cuando tengamos login: JWTTokenManagerInterface $jwtManager, Request $request
        $mensajes = $mensajeRepository->findAll();

        return $this->json($mensajes);
    }
    #[Route('/listar', name: 'api_mensaje_list', methods: ['POST'])]
    public function list(EntityManagerInterface $entityManager, MensajeRepository $mensajeRepository, Request $request): JsonResponse
    { //añadir esto a los variables que entra cuando tengamos login: JWTTokenManagerInterface $jwtManager, Request $request
        $data = json_decode($request->getContent(), true);
        $usuarios = $entityManager->getRepository(Usuario::class)->findBy(["username"=>$data['username']]);
        $usuario = $usuarios[0];
        $mensajes = $mensajeRepository->getMensajes(["id" => $usuario->getId(), "id2"=>$data['usuario_receptor']]);
        foreach ($mensajes as $m){
            if ($m['id_usuario_receptor'] == $usuario->getId()) {
                $mesaje = $mensajeRepository->find($m['id']);
                $mesaje->setLeido(true);
                $entityManager->flush();
            }
        }
        return $this->json($mensajes);
    }

    #[Route('/get/{id}', name: 'api_mensaje_show', methods: ['GET'])]
    public function show(Mensaje $mensaje): JsonResponse
    {
        return $this->json($mensaje);
    }
    #[Route('/buscar', name: 'api_mensaje_busca', methods: ['POST'])]
    public function search(EntityManagerInterface $entityManager,MensajeRepository $mensajeRepository,CanalRepository $canalRepository,Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $usuarios = $entityManager->getRepository(Usuario::class)->findBy(["username"=>$data['username']]);
        $usuario = $usuarios[0];
        $mensajes = $mensajeRepository->getBusqueda(["id" => $usuario->getId()]);
        $canales = $canalRepository->canalMensaje($mensajes);
        $lista = [];
        foreach ($mensajes as $m){
            $sinleer = $entityManager->getRepository(Mensaje::class)->findBy(["usuario_emisor"=>$m,"usuario_receptor"=>$usuario,"leido"=>false]);
            foreach ($canales as $c){
                if ($c->getUsuario()->getId() == $m['id']){
                    $elemento = ["canal"=>$c,"numero"=>count($sinleer)];
                    array_push($lista, $elemento);
                }
            }
        }
        return $this->json($lista);
    }

    #[Route('/crear', name: 'api_mensaje_create', methods: ['POST'])]
    public function create(NotificacionController $notificacionController,EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $mensaje = new Mensaje();
        $mensaje->setTexto($data['texto']);
        $mensaje->setFecha(new DateTime());
        $usuarios = $entityManager->getRepository(Usuario::class)->findBy(["username"=>$data['username']]);
        $usuario = $usuarios[0];
        $usuarioemisor = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $usuario->getId()]);
        $mensaje->setUsuarioEmisor($usuarioemisor[0]);
        $usuarioreceptor = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $data["usuario_receptor"]]);
        $mensaje->setUsuarioReceptor($usuarioreceptor[0]);

        $entityManager->persist($mensaje);
        $entityManager->flush();
        $lista = [$usuarioreceptor[0],5,"Nuevo mensaje"];
        $notificacionController->crear($entityManager,$lista);
        return $this->json(['message' => 'Mensaje creado'], Response::HTTP_CREATED);
    }

    #[Route('/crearlo', name: 'api_mensaje_createlo', methods: ['POST'])]
    public function createlo(NotificacionController $notificacionController,EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $mensaje = new Mensaje();
        $mensaje->setTexto($data['texto']);
        $mensaje->setFecha(new DateTime());
        $usuarios = $entityManager->getRepository(Usuario::class)->findBy(["username"=>$data['username']]);
        $usuario = $usuarios[0];
        $usuarios2 = $entityManager->getRepository(Usuario::class)->findBy(["username"=>$data['usernamesecundario']]);
        $usuario2 = $usuarios2[0];
        $usuarioemisor = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $usuario->getId()]);
        $mensaje->setUsuarioEmisor($usuarioemisor[0]);
        $usuarioreceptor = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $usuario2->getId()]);
        $mensaje->setUsuarioReceptor($usuarioreceptor[0]);

        $entityManager->persist($mensaje);
        $entityManager->flush();
        $lista = [$usuarioreceptor[0],5,"Nuevo mensaje"];
        $notificacionController->crear($entityManager,$lista);
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
    #[Route('/leer', name: "api_leer_mensaje", methods: ["post"])]
    public function lectura(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {



        return $this->json(['message' => 'Mensaje eliminado'], Response::HTTP_OK);

    }

    #[Route('/estadisticas', name: "estadisticas", methods: ["POST"])]
    public function estadisticas(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request-> getContent(), true);
        $mensajesEnviados = $entityManager->getRepository(Mensaje::class)->enviados(['id' => $data]);
        $mensajesRecibidos = $entityManager->getRepository(Mensaje::class)->recibidos(['id' => $data]);

        return $this->json(['enviados' => $mensajesEnviados, 'recibidos' => $mensajesRecibidos]);
    }

}
