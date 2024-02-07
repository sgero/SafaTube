<?php

namespace App\Controller;

use App\DTO\FiltroDTO;
use App\Entity\Canal;
use App\Entity\Comentario;
use App\Entity\Like;
use App\Entity\TipoCategoria;
use App\Entity\TipoNotificacion;
use App\Entity\TipoPrivacidad;
use App\Entity\Usuario;
use App\Entity\Video;
use App\Repository\VideoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PgSql\Connection;
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

        return $this->json(['message' => $videos], Response::HTTP_CREATED);
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
        $videoNuevo->setMiniatura($data['miniatura']);

        $tipoCategoria = $entityManager->getRepository(TipoCategoria::class)->findBy(["nombre"=> $data["tipoCategoria"]]);
        $videoNuevo->setTipoCategoria($tipoCategoria[0]);

        $tipoPrivacidad = $entityManager->getRepository(TipoPrivacidad::class)->findBy(["nombre"=> $data["tipoPrivacidad"]]);
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
        $video->setMiniatura($data['miniatura']);

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

        return $this->json($listaVideos, Response::HTTP_OK);
    }

    #[Route('/buscar', name: "buscar_video", methods: ["POST"])]
    public function findVideos(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = $request->getContent();
        $listaVideos = $entityManager->getRepository(Video::class)->findVideos(["titulo"=> $data]);
        $listaCanales = $entityManager->getRepository(Canal::class)->findCanales(["nombre"=> $data]);
        $filtro = new FiltroDTO();
        $filtro->setVideos(new ArrayCollection($listaVideos));
        $filtro->setCanales(new ArrayCollection($listaCanales));

        return $this->json($filtro, Response::HTTP_OK);
    }

    #[Route('/getVideosRecomendadosAPartirDeVideo', name: "get_videos_recomendado_video", methods: ["POST"])]
    public function getVideosRecomendadosAPartirDeVideo(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $listaVideos = $entityManager->getRepository(Video::class)->getVideosRecomendadosAPartirDeVideo(["id"=> $data]);

        return $this->json(['videos' => $listaVideos], Response::HTTP_OK);
    }

    #[Route('/getVideosRecomendados', name: "get_videos_recomendados", methods: ["POST"])]
    public function getVideosRecomendados(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $listaVideos = $entityManager->getRepository(Video::class)->getVideosRecomendados(["id"=> $data]);

        return $this->json(['videos' => $listaVideos], Response::HTTP_OK);
    }

    #[Route('/getVideosCanalesSuscritos', name: "get_videos_suscritos", methods: ["POST"])]
    public function getVideosCanalesSuscritos(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $listaVideos = $entityManager->getRepository(Video::class)->getVideosCanalesSuscritos(["id"=> $data]);

        return $this->json(['videos' => $listaVideos], Response::HTTP_OK);
    }
    #[Route('/getComentariosLista', name: "get_comentarios_lista", methods: ["POST"])]
    public function getComentariosLista(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $listaComentarios = $entityManager->getRepository(Video::class)->getComentariosLista(["id"=> $data]);

        return $this->json(['videos' => $listaComentarios], Response::HTTP_OK);
    }
    #[Route('/getRespuestaComentariosLista', name: "get_respuestas_lista", methods: ["POST"])]
    public function getRespuestaComentariosLista(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $listaRespuestas = $entityManager->getRepository(Video::class)->getRespuestaComentariosLista(["id"=> $data["id"]]);

        return $this->json(['videos' => $listaRespuestas], Response::HTTP_OK);
    }

    #[Route('/valorar', name: 'valorar_video', methods: ['POST'])]
    public function valorarVideo(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $like = new Like();

        $usuario = $entityManager->getRepository(Usuario::class)->findBy(["id"=> $data["usuario"]]);
        $like->setUsuario($usuario[0]);

        $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]]);
        $like->setVideo($video[0]);

        $entityManager->persist($like);
        $entityManager->flush();

        return $this->json(['message' => 'Like creado'], Response::HTTP_CREATED);
    }


    #[Route('/añadirVisita', name: 'añadir_visita', methods: ['POST'])]
    public function anyadirVisita(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]]);
        $totalVisitas = $entityManager->getRepository(Video::class)->getVisitas(["id"=> $data["video"]]);
        $insertarVisitas = $entityManager->getRepository(Video::class)->anyadirVisita(["id"=> $data]);

        $video[0]->setTotalVisitas($totalVisitas[0]["count"]);

        $entityManager->persist($video[0]);
        $entityManager->flush();

        return $this->json(['videos' => $video, 'visitas' => $insertarVisitas], Response::HTTP_OK);
    }

}
