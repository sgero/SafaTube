<?php

namespace App\Controller;

use App\DTO\FiltroDTO;
use App\Entity\Canal;
use App\Entity\Comentario;
use App\Entity\Like;
use App\Entity\Suscripcion;
use App\Entity\TipoCategoria;
use App\Entity\TipoNotificacion;
use App\Entity\TipoPrivacidad;
use App\Entity\Usuario;
use App\Entity\Video;
use App\Repository\VideoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    #[Route('/get', name: 'video_by_id', methods: ['POST'])]
    public function getById(EntityManagerInterface $entityManager,Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data]);
        return $this->json($video[0]);
    }

    #[Route('/crear', name: 'crear_video', methods: ['POST'])]
    public function crear(NotificacionController $notificacionController,EntityManagerInterface $entityManager, Request $request,VideoRepository $videoRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $videoNuevo = new Video();
        $videoNuevo->setTitulo($data['titulo']);
        $videoNuevo->setDescripcion($data['descripcion']);
        $videoNuevo->setDuracion($data['duracion']);
        $videoNuevo->setFecha(date('Y-m-d H:i:s'));
        $videoNuevo->setEnlace($data['enlace']);
        $videoNuevo->setMiniatura($data['miniatura']);

        $tipoCategoria = $entityManager->getRepository(TipoCategoria::class)->findBy(["nombre"=> $data["tipoCategoria"]]);
        $videoNuevo->setTipoCategoria($tipoCategoria[0]);

        $tipoPrivacidad = $entityManager->getRepository(TipoPrivacidad::class)->findBy(["nombre"=> $data["tipoPrivacidad"]]);
        $videoNuevo->setTipoPrivacidad($tipoPrivacidad[0]);

        $canal = $entityManager->getRepository(Canal::class)->findBy(["id"=> $data["canal"]["id"]]);
        $videoNuevo->setCanal($canal[0]);

        $entityManager->persist($videoNuevo);
        $entityManager->flush();

        $lista = [$canal[0]->getUsuario(),2,"Nuevo contenido"];
        $notificacionController->crear($entityManager,$lista);

        return $this->json(['message' => 'Video creado correctamente'], Response::HTTP_CREATED);
    }

    #[Route('/editar/{id}', name: "editar_video", methods: ['PUT'])]
    public function editar(EntityManagerInterface $entityManager, Request $request, Video $video):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $video->setTitulo($data['titulo']);
        $video->setDescripcion($data['descripcion']);
        $video->setDuracion($data['duracion']);
        $video->setFecha(date('Y-m-d H:i:s'));
        $video->setMiniatura($data['miniatura']);

        $tipoCategoria = $entityManager->getRepository(TipoCategoria::class)->findBy(["nombre"=> $data["tipoCategoria"]]);
        $video->setTipoCategoria($tipoCategoria[0]);

        $tipoPrivacidad = $entityManager->getRepository(TipoPrivacidad::class)->findBy(["nombre"=> $data["tipoPrivacidad"]]);
        $video->setTipoPrivacidad($tipoPrivacidad[0]);

        $canal = $entityManager->getRepository(Canal::class)->findBy(["id"=> $data["canal"]["id"]]);
        $video->setCanal($canal[0]);

        $entityManager->flush();

        return $this->json(['message' => 'Video modificado'], Response::HTTP_OK);
    }

    #[Route('/eliminar', name: "borrar_video", methods: ["POST"])]
    public function eliminar (EntityManagerInterface $entityManager, Request $video):JsonResponse
    {
        $data = json_decode($video->getContent(), true);

        $videoBorrar = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["id"]]);
        $videoBorrar[0]->setActivo(false);
        $entityManager->persist($videoBorrar[0]);
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
//        $canal = $entityManager->getRepository(Canal::class)->canalContieneVideo($data);

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

    #[Route('/getHistorial', name: "get_historial", methods: ["POST"])]
    public function getHistorial(EntityManagerInterface $entityManager, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $listaVideos = $entityManager->getRepository(Video::class)->getHistorial(["id"=> $data]);

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


    #[Route('/anyadirVisita', name: 'añadir_visita', methods: ['POST'])]
    public function anyadirVisita(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]]);
        $totalVisitas = $entityManager->getRepository(Video::class)->getVisitas(["id"=> $data["video"]]);
        $insertarVisitas = $entityManager->getRepository(Video::class)->anyadirVisita(["id"=> $data]);

        $video[0]->setTotalVisitas($totalVisitas[0]["count"] +1);

        $entityManager->persist($video[0]);
        $entityManager->flush();

        return $this->json(['videos' => $video, 'visitas' => $insertarVisitas], Response::HTTP_OK);
    }





//    public function configuracionPrivacidad(EntityManagerInterface $entityManager, Request $request): JsonResponse
//    {
//        $data = json_decode($request->getContent(), true);
//
//        $video = $entityManager->getRepository(Video::class)->findBy(["id"=> $data["video"]]);
//        $video[0]->setTipoPrivacidad($data["tipoPrivacidad"]);
//
//        $entityManager->persist($video[0]);
//        $entityManager->flush();
//
//        return $this->json(['message' => 'Privacidad cambiada'], Response::HTTP_OK);
//    }
    #[Route('/confprivacy', name: 'configuracion_privacidad', methods: ['POST'])]
    public function sendConfPrivacy( request $request, EntityManagerInterface $entityManager){

        $data = json_decode($request->getContent(), true);
        $usuarios = $entityManager->getRepository(Usuario::class)->findBy(["username"=> $data["username"]]);
        $usuario = $usuarios[0];
        $canales = $entityManager->getRepository(Canal::class)->findBy(["usuario"=> $usuario]);
        $canal = $canales[0];

        if ($data["accessToPrivateVideos"] == "true"){
            $tipos = $entityManager->getRepository(TipoPrivacidad::class)->findBy(["id"=> 2]);
            $videos = $entityManager->getRepository(Video::class)->findBy(["canal"=> $canal, "tipoPrivacidad"=> $tipos[0]]);
            $tipo = $entityManager->getRepository(TipoPrivacidad::class)->findBy(["id"=> 3]);
        }else{
            $tipos = $entityManager->getRepository(TipoPrivacidad::class)->findBy(["id"=> 3]);
            $videos = $entityManager->getRepository(Video::class)->findBy(["canal"=> $canal, "tipoPrivacidad"=> $tipos[0]]);
            $tipo = $entityManager->getRepository(TipoPrivacidad::class)->findBy(["id"=> 2]);
        }
        foreach ($videos as $video){

           $video->setTipoPrivacidad($tipo[0]);
//              $entityManager->persist($video);
                $entityManager->flush();
        }
        return $this->json(['message' => 'Privacidad cambiada'], Response::HTTP_OK);
    }
}
