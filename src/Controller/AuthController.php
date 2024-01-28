<?php

namespace App\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Annotation\Route;
//
//class AuthController extends AbstractController
//{
//    #[Route('/auth', name: 'app_auth')]
//    public function index(): Response
//    {
//        return $this->render('auth/index.html.twig', [
//            'controller_name' => 'AuthController',
//        ]);
//    }
//}

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route("/login", name: "login")]

    public function login(AuthenticationUtils $authenticationUtils)
    {
        // Obtiene el error de inicio de sesión (si hay alguno)
        $error = $authenticationUtils->getLastAuthenticationError();

        // Último nombre de usuario ingresado (si hay alguno)
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route("/logout", name:"logout")]

    public function logout()
    {
        // Este controlador no se ejecutará,
        // ya que la ruta es manejada por el sistema de seguridad.
    }
}