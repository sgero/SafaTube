<?php

namespace App\Controller;

use App\Entity\Canal;
use App\Entity\TipoCategoria;
use App\Entity\TipoNotificacion;
use App\Entity\TipoPrivacidad;
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
    #[Route('/listar', name: 'listar_video', methods: ['GET'])]
    public function list(VideoRepository $videoRepository): JsonResponse
    {
        $videos = $videoRepository->findAll();

        return $this->json($videos);
    }

    #[Route('/get/{id}', name: 'video_by_id', methods: ['GET'])]
    public function getById(Video $video): JsonResponse
    {
        return $this->json($video);
    }

    #[Route('/crear', name: 'crear_video', methods: ['POST'])]
    public function crear(EntityManagerInterface $entityManager, Request $request,VideoRepository $videoRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $videoNuevo = new Video();
        $videoNuevo->setTitulo($data['titulo']);
        $videoNuevo->setDescripcion($data['descripcion']);
        $videoNuevo->setDuracion($data['duracion']);
        $videoNuevo->setFecha($data['fecha']); //la fecha viene en formato 'd/m/Y'
        $videoNuevo->setEnlace($data['enlace']);

        $tipoCategoria = $entityManager->getRepository(TipoCategoria::class)->findBy(["id"=> $data["tipo_categoria"]]);
        $videoNuevo->setTipoCategoria($tipoCategoria[0]);

        $tipoPrivacidad = $entityManager->getRepository(TipoPrivacidad::class)->findBy(["id"=> $data["tipo_privacidad"]]);
        $videoNuevo->setTipoPrivacidad($tipoPrivacidad[0]);

        $canal = $entityManager->getRepository(Canal::class)->findBy(["id"=> $data["canal"]]);
        $videoNuevo->setCanal($canal[0]);

        $entityManager->persist($videoNuevo);
        $entityManager->flush();

        return $this->json(['message' => 'Video creado correctamente'], Response::HTTP_CREATED);
    }

    #[Route('/editar/{id}', name: "editar_video", methods: ["PUT"])]
    public function editar(EntityManagerInterface $entityManager, Request $request, Video $video):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $video->setTitulo($data['titulo']);
        $video->setDescripcion($data['descripcion']);
        $video->setDuracion($data['duracion']);
        $video->setFecha($data['fecha']); //la fecha viene en formato 'd/m/Y'
        $video->setEnlace($data['enlace']);

        $tipoCategoria = $entityManager->getRepository(TipoCategoria::class)->findBy(["id"=> $data["tipo_categoria"]]);
        $video->setTipoCategoria($tipoCategoria[0]);

        $tipoPrivacidad = $entityManager->getRepository(TipoPrivacidad::class)->findBy(["id"=> $data["tipo_privacidad"]]);
        $video->setTipoPrivacidad($tipoPrivacidad[0]);

        $canal = $entityManager->getRepository(Canal::class)->findBy(["id"=> $data["canal"]]);
        $video->setCanal($canal[0]);

        $entityManager->flush();

        return $this->json(['message' => 'Video modificado'], Response::HTTP_OK);
    }

    #[Route('/eliminar/{id}', name: "borrar_video", methods: ["DELETE"])]
    public function deleteById(EntityManagerInterface $entityManager, Video $video):JsonResponse
    {

        $entityManager->remove($video);
        $entityManager->flush();

        return $this->json(['message' => 'Video eliminado'], Response::HTTP_OK);

    }

    #[Route('/por_canal', name: "buscar_videos_canal", methods: ["POST"])]
    public function getVideosPorCanal(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $listaVideos = $entityManager->getRepository(Video::class)->findVideosPorCanal(["id"=> $data["id"]]);

        return $this->json(['videos' => $listaVideos], Response::HTTP_OK);
    }

    #[Route('/por_categoria', name: "buscar_videos_categoria", methods: ["POST"])]
    public function getVideosPorCategoria(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $listaVideos = $entityManager->getRepository(Video::class)->findVideosPorCategoria(["nombre"=> $data["nombre"]]);

        return $this->json(['videos' => $listaVideos], Response::HTTP_OK);
    }

    #[Route('/buscar', name: "buscar_video_1", methods: ["POST"])]
    public function findVideos(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = $request->getContent();
        $listaVideos = $entityManager->getRepository(Video::class)->findVideos(["titulo"=> $data]);

        return $this->json(['videos' => $listaVideos], Response::HTTP_OK);
    }

}
