<?php

namespace App\Controller;

use App\Entity\Canal;
use App\Entity\Notificacion;
use App\Entity\TipoNotificacion;
use App\Repository\NotificacionRepository;
use \DateTime;
use Doctrine\ORM\EntityManagerInterface;
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
        $canales = $entityManager->getRepository(Canal::class)->findBy(["usuario"=>$data['id']]);
        $canal = $canales[0];
        $notificacion = $notificacionRepository->getcountmensaje((int)["id" => $canal->getId()]);
        $notificacionAtender = $entityManager->getRepository(Notificacion::class)->findBy(["canal"=>$canal->getId()]);
        $notificacionAtender[0]->setAtendida(true);
        $entityManager->flush();
        return $this->json($notificacion[0]);
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
