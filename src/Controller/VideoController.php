<?php

namespace App\Controller;

use App\Entity\Canal;
use App\Entity\Video;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/video')]
class VideoController extends AbstractController
{
    #[Route('', name: 'api_video_list', methods: ['GET'])]
    public function list(VideoRepository $videoRepository): JsonResponse
    {
        $videos = $videoRepository->findAll();

        return $this->json($videos);
    }

    #[Route('/{id}', name: 'api_video_show', methods: ['GET'])]
    public function show(Video $video): JsonResponse
    {
        return $this->json($video);
    }

    #[Route('', name: 'api_video_create', methods: ['POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request,TurnoRepository $turnoRepository, TipoMonitorRepository $tipoMonitorRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $video = new Video();
        $video->setTitulo($data['titulo']);
        $video->setDescripcion($data['descripcion']);
        $video->setDuracion($data['duracion']);
        $video->setFecha($data['fecha']); //la fecha viene en formato 'd/m/Y'
        $canal = $entityManager->getRepository(Canal::class)->findBy(["id"=> $data["id_canal"]]);
        $video->setCanal($canal[0]);

        $entityManager->persist($video);
        $entityManager->flush();

        return $this->json(['message' => 'Video creado correctamente'], Response::HTTP_CREATED);
    }


}
