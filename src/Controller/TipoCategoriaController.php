<?php

namespace App\Controller;

use App\Repository\TipoCategoriaRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/categoria')]
class TipoCategoriaController extends AbstractController{
    #[Route('/listar', name: 'listar_categoria', methods: ['GET'])]
    public function list(TipoCategoriaRepository $tipoCategoriaRepository): JsonResponse
    {
        $categorias = $tipoCategoriaRepository->findAll();

        return $this->json($categorias);
    }
}
