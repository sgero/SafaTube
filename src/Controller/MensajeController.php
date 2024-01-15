<?php

namespace App\Controller;

use App\Entity\Mensaje;
use App\Repository\MensajeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/mensaje')]
class MensajeController extends AbstractController
{
    #[Route('', name: 'api_mensaje_list', methods: ['GET'])]
    public function list(MensajeRepository $mensajeRepository): JsonResponse
    {
        $mensajes = $mensajeRepository->findAll();
    
        return $this->json($mensajes);
    }

}