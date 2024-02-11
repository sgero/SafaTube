<?php

namespace App\Controller;

use App\Entity\Canal;
use App\Entity\Notificacion;
use App\Entity\TipoNotificacion;
use App\Entity\Usuario;
use App\Repository\NotificacionRepository;
use \DateTime;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/notificacion')]
class NotificacionController extends AbstractController
{

    #[Route('/listar', name: "notificacion_list", methods: ["GET"])]
    public function list(NotificacionRepository $notificacionRepository):JsonResponse
    {
        $list = $notificacionRepository->findAll();

        return $this->json($list);
    }

    #[Route('/get/{id}', name: "notificacion_by_id", methods: ["GET"])]
    public function getById(Notificacion $notificacion):JsonResponse
    {
        return $this->json($notificacion);
    }
    #[Route('/contar_mensaje', name: "contar_mensaje", methods: ["POST"])]
    public function contarMensajes(NotificacionRepository $notificacionRepository,EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $usuarios = $entityManager->getRepository(Usuario::class)->findBy(["username"=>$data['username']]);
        $usuario = $usuarios[0];
        $canales = $entityManager->getRepository(Canal::class)->findBy(["usuario"=>$usuario->getId()]);
        $canal = $canales[0];
        $numero = $canal->getId();
        $notificacion = $notificacionRepository->getcountmensaje($numero);
        return $this->json($notificacion[0]);
    }

    #[Route('/contar_like', name: "contar_like", methods: ["POST"])]
    public function contarlikes(NotificacionRepository $notificacionRepository,EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $usuarios = $entityManager->getRepository(Usuario::class)->findBy(["username"=>$data['username']]);
        $usuario = $usuarios[0];
        $canales = $entityManager->getRepository(Canal::class)->findBy(["usuario"=>$usuario->getId()]);
        $canal = $canales[0];
        $numero = $canal->getId();
        $notificacion = $notificacionRepository->getcountlike($numero);
        return $this->json($notificacion[0]);
    }

    #[Route('/contar_dislike', name: "contar_dislike", methods: ["POST"])]
    public function contarDislikes(NotificacionRepository $notificacionRepository,EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $usuarios = $entityManager->getRepository(Usuario::class)->findBy(["username"=>$data['username']]);
        $usuario = $usuarios[0];
        $canales = $entityManager->getRepository(Canal::class)->findBy(["usuario"=>$usuario->getId()]);
        $canal = $canales[0];
        $numero = $canal->getId();
        $notificacion = $notificacionRepository->getcountDislike($numero);
        return $this->json($notificacion[0]);
    }
    #[Route('/contarsubs', name: "contar_subs", methods: ["POST"])]
    public function contarsubs(NotificacionRepository $notificacionRepository,EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $usuarios = $entityManager->getRepository(Usuario::class)->findBy(["username"=>$data['username']]);
        $usuario = $usuarios[0];
        $canales = $entityManager->getRepository(Canal::class)->findBy(["usuario"=>$usuario->getId()]);
        $canal = $canales[0];
        $numero = $canal->getId();
        $notificacion = $notificacionRepository->getcountSubs($numero);
        return $this->json($notificacion[0]);
    }
    #[Route('/notificacion', name: "notificacion", methods: ["POST"])]
    public function notificar(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {   $campana = false;
        $data = json_decode($request->getContent(), true);
        $usuarios = $entityManager->getRepository(Usuario::class)->findBy(["username"=>$data['username']]);
        $usuario = $usuarios[0];
        $canales = $entityManager->getRepository(Canal::class)->findBy(["usuario"=>$usuario->getId()]);
        $canal = $canales[0];
        $notificacionAtender = $entityManager->getRepository(Notificacion::class)->findBy(["canal"=>$canal->getId(),"atendida" => false]);
        if (!empty($notificacionAtender)){
            $campana = true;
        }

        return $this->json($campana);
    }
    #[Route('/atendidas', name: "atendidas", methods: ["POST"])]
    public function atender(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $usuarios = $entityManager->getRepository(Usuario::class)->findBy(["username"=>$data['username']]);
        $usuario = $usuarios[0];
        $canales = $entityManager->getRepository(Canal::class)->findBy(["usuario"=>$usuario->getId()]);
        $canal = $canales[0];
        $notificacionAtender = $entityManager->getRepository(Notificacion::class)->findBy(["canal"=>$canal->getId(),"atendida" => false]);
        foreach ($notificacionAtender as $n){
            $n->setAtendida(true);
            $entityManager->flush();
        }

        return $this->json(['mensaje' => 'Notificacion atendida']);
    }
    //#[Route('/crear', name: "crear_notificacion", methods: ["POST"])]
    public function crear($entityManager, $request):JsonResponse
    {
        //$json = json_decode($request-> getContent(), true);

        $nuevaNotificacion = new Notificacion();
        $nuevaNotificacion->setMensaje($request[2]);
        $nuevaNotificacion->setFecha(new DateTime());
        $tipo = $entityManager->getRepository(TipoNotificacion::class)->findBy(["id"=> $request[1]]);
        $nuevaNotificacion->setTipoNotificacion($tipo[0]);
        $canal = $entityManager->getRepository(Canal::class)->findBy(["usuario"=> $request[0]]);
        $nuevaNotificacion->setCanal($canal[0]);

        $entityManager->persist($nuevaNotificacion);
        $entityManager->flush();

        return $this->json(['message' => 'Notificacion creada'], Response::HTTP_CREATED);
    }


    #[Route('/{id}', name: "editar_notificacion", methods: ["PUT"])]
    public function editar(EntityManagerInterface $entityManager, Request $request, Notificacion $notificacion):JsonResponse
    {
        $json = json_decode($request-> getContent(), true);

        $notificacion = new Notificacion();
        $notificacion->setMensaje($json["mensaje"]);
        $notificacion->setFecha(new DateTime());



        $entityManager->flush();

        return $this->json(['message' => 'Notificacion modificada'], Response::HTTP_OK);
    }

    #[Route('/eliminar/{id}', name: "api_delete_by_id", methods: ["DELETE"])]
    public function deleteById(EntityManagerInterface $entityManager, Notificacion $notificacion):JsonResponse
    {
        $entityManager->remove($notificacion);
        $entityManager->flush();

        return $this->json(['message' => 'Notificacion eliminada'], Response::HTTP_OK);
    }




}
