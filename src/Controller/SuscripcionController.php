<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SuscripcionController extends AbstractController
{
    #[Route('/suscripcion', name: 'app_suscripcion')]
    public function index(): Response
    {
        return $this->render('suscripcion/index.html.twig', [
            'controller_name' => 'SuscripcionController',
        ]);
    }
}
