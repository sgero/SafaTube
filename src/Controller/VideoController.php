<?php

namespace App\Controller;

use App\Entity\Canal;
use App\Entity\Video;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/video')]
class VideoController extends AbstractController
{
    #[Route('', name: 'listar_video', methods: ['GET'])]
    public function list(VideoRepository $videoRepository): JsonResponse
    {
        $videos = $videoRepository->findAll();

        return $this->json($videos);
    }

    #[Route('/{id}', name: 'video_by_id', methods: ['GET'])]
    public function getById(Video $video): JsonResponse
    {
        return $this->json($video);
    }

    #[Route('', name: 'crear_video', methods: ['POST'])]
    public function crear(EntityManagerInterface $entityManager, Request $request,VideoRepository $videoRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $videoNuevo = new Video();
        $videoNuevo->setTitulo($data['titulo']);
        $videoNuevo->setDescripcion($data['descripcion']);
        $videoNuevo->setDuracion($data['duracion']);
        $videoNuevo->setFecha($data['fecha']); //la fecha viene en formato 'd/m/Y'
        $videoNuevo->setEnlace($data['enlace']);
        $videoNuevo->setActivo(true);

        $canal = $entityManager->getRepository(Canal::class)->findBy(["id"=> $data["canal"]]);
        $videoNuevo->setCanal($canal[0]);

        $entityManager->persist($videoNuevo);
        $entityManager->flush();

        return $this->json(['message' => 'Video creado correctamente'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: "editar_video", methods: ["PUT"])]
    public function editar(EntityManagerInterface $entityManager, Request $request, Video $video):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $video->setTitulo($data['titulo']);
        $video->setDescripcion($data['descripcion']);
        $video->setDuracion($data['duracion']);
        $video->setFecha($data['fecha']); //la fecha viene en formato 'd/m/Y'
        $video->setEnlace($data['enlace']);

        $canal = $entityManager->getRepository(Canal::class)->findBy(["id"=> $data["canal"]]);
        $video->setCanal($canal[0]);

        $entityManager->flush();

        return $this->json(['message' => 'Video modificado'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: "borrar_video", methods: ["DELETE"])]
    public function deleteById(EntityManagerInterface $entityManager, Video $video):JsonResponse
    {

        $entityManager->remove($video);
        $entityManager->flush();

        return $this->json(['message' => 'Video eliminado'], Response::HTTP_OK);

    }

}
