<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/mensaje')]
class MensajeController extends AbstractController
{
    #[Route('', name: 'api_mensaje_list', methods: ['GET'])]
    public function list(Mensa $mensajeRepository): JsonResponse
    {
        $mensajes = $mensajeRepository->findAll();
    
        return $this->json($monitores);
    }

}