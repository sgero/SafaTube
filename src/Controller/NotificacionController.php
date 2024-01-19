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

    #[Route('', name: "notificacion_list", methods: ["GET"])]
    public function list(NotificacionRepository $notificacionRepository):JsonResponse
    {
        $list = $notificacionRepository->findAll();

        return $this->json($list);
    }

    #[Route('/{id}', name: "notificacion_by_id", methods: ["GET"])]
    public function getById(Notificacion $notificacion):JsonResponse
    {
        return $this->json($notificacion);
    }

    #[Route('', name: "crear_notificacion", methods: ["POST"])]
    public function crear(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $json = json_decode($request-> getContent(), true);

        $nuevaNotificacion = new Notificacion();
        $nuevaNotificacion->setMensaje($json["mensaje"]);
        $nuevaNotificacion->setFecha(new DateTime());
        $tipo = $entityManager->getRepository(TipoNotificacion::class)->findBy(["id"=> $json["tipoNotificacion"]]);
        $nuevaNotificacion->setTipoNotificacion($tipo[0]);
        $canal = $entityManager->getRepository(Canal::class)->findBy(["id"=> $json["canal"]]);
        $nuevaNotificacion->setCanal($canal[0]);

        $entityManager->persist($nuevaNotificacion);
        $entityManager->flush();

        return $this->json(['message' => 'Notificacion creada'], Response::HTTP_CREATED);
    }


    #[Route('/{id}', name: "editar_notificacion", methods: ["PUT"])]
    public function editar(EntityManagerInterface $entityManager, Request $request, Notificacion $notificacion):JsonResponse
    {
        $json = json_decode($request-> getContent(), true);

        $nuevaNotificacion = new Notificacion();
        $nuevaNotificacion->setMensaje($json["mensaje"]);
        $nuevaNotificacion->setFecha($json["fecha"]);

        $canal = $entityManager->getRepository(Canal::class)->findBy(["id"=> $json["canal"]]);
        $nuevaNotificacion->setCanal($canal[0]);

        $entityManager->flush();

        return $this->json(['message' => 'Notificacion modificada'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: "delete_by_id", methods: ["DELETE"])]
    public function deleteById(EntityManagerInterface $entityManager, Notificacion $notificacion):JsonResponse
    {
        $entityManager->remove($notificacion);
        $entityManager->flush();

        return $this->json(['message' => 'Notificacion eliminada'], Response::HTTP_OK);
    }




}
