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

use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController


// Login con formulario

//{
//    #[Route("/login", name: "login")]
//
//    public function login(AuthenticationUtils $authenticationUtils): \Symfony\Component\HttpFoundation\Response
//    {
//        // Obtiene el error de inicio de sesión (si hay alguno)
//        $error = $authenticationUtils->getLastAuthenticationError();
//
//        // Último nombre de usuario ingresado (si hay alguno)
//        $lastUsername = $authenticationUtils->getLastUsername();
//
//        return $this->render('auth/login.html.twig', [
//            'last_username' => $lastUsername,
//            'error'         => $error,
//        ]);
//    }
//
//    #[Route("/logout", name:"logout")]
//
//    public function logout()
//    {
//        // Este controlador no se ejecutará,
//        // ya que la ruta es manejada por el sistema de seguridad.
//    }
//}



//Login con JSON y JWT
//{
//    #[Route("/login", name: "login")]
//    public function login()
//    {
//        // Este controlador no necesita lógica adicional
//        // ya que la autenticación se maneja a través de json_login y JWT.
//        return $this->json(['message' => 'Login successful']);
//    }
//
//    #[Route("/logout", name: "logout")]
//    public function logout()
//    {
//        // Este controlador no se ejecutará ya que el logout es manejado por el sistema de seguridad.
//        // No necesitas agregar lógica adicional aquí.
//    }
//}

//    #[Route('/login', name: "login", methods: ["POST"])]
//    public function login(Request $request, UsuarioRepository $usuarioRepository, UserPasswordHasherInterface $passwordHasher):JsonResponse
//    {
//        $json = json_decode($request-> getContent(), true);
//
//        $usuario = $usuarioRepository->findOneBy(["username" => $json["username"]]);
//
//        if(!$usuario){
//            return $this->json(['message' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
//        }
//
//        if(!$passwordHasher->isPasswordValid($usuario, $json["password"])){
//            return $this->json(['message' => 'Contraseña incorrecta'], Response::HTTP_BAD_REQUEST);
//        }
//
//        return $this->json(['message' => 'Bienvenido'], Response::HTTP_OK);
//    }

{
    #[Route('/login', name: "login", methods: ["POST"])]
    public function login(Request $request, UsuarioRepository $usuarioRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $json = json_decode($request->getContent(), true);

        $usuario = $usuarioRepository->findOneBy(["username" => $json["username"]]);

        if (!$usuario) {
            return $this->json(['message' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        // Verificar si el usuario está verificado
        if (!$usuario->getIsVerified()) {
            return $this->json(['message' => 'Usuario no verificado'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$passwordHasher->isPasswordValid($usuario, $json["password"])) {
            return $this->json(['message' => 'Contraseña incorrecta'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['message' => 'Bienvenido'], Response::HTTP_OK);
    }



//    #[Route('/recuperarpwd', name: "recuperarpwd", methods: ["POST"])]
//    public function recuperarpwd(Request $request, UsuarioRepository $usuarioRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse
//    {
//        $json = json_decode($request->getContent(), true);
//
//        $usuario = $usuarioRepository->findOneBy(["username" => $json["username"]]);
//
//        if (!$usuario) {
//            return $this->json(['message' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
//        }
//
//        // Verificar si el usuario está verificado
//        if (!$usuario->getIsVerified()) {
//            return $this->json(['message' => 'Usuario no verificado'], Response::HTTP_UNAUTHORIZED);
//        }
//
//        if (!$passwordHasher->isPasswordValid($usuario, $json["password"])) {
//            return $this->json(['message' => 'Contraseña incorrecta'], Response::HTTP_BAD_REQUEST);
//        }
//
//        return $this->json(['message' => 'Bienvenido'], Response::HTTP_OK);
//}

//    #[Route('/recuperarpwd', name: "recuperarpwd", methods: ["POST"])]
//    public function recuperarpwd(Request $request, UsuarioRepository $usuarioRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse
//    {
//        $json = json_decode($request->getContent(), true);
//
//        // Buscar al usuario por nombre de usuario o correo electrónico
//        $usuario = $usuarioRepository->findOneBy([
//            'username' => $json['username'],
//            // Agregar búsqueda por correo electrónico
//            'email' => $json['email']
//        ]);
//
//        if (!$usuario) {
//            return $this->json(['message' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
//        }
//
//        // Verificar si el usuario está verificado
//        if (!$usuario->getIsVerified()) {
//            return $this->json(['message' => 'Usuario no verificado'], Response::HTTP_UNAUTHORIZED);
//        }
//
//        // Generar y enviar la nueva contraseña al usuario
//        $nuevaContrasena = $this->generarNuevaContrasena(); // Implementa la lógica para generar una nueva contraseña
//        $usuario->setPassword($passwordHasher->hashPassword($usuario, $nuevaContrasena)); // Asignar la nueva contraseña hasheada al usuario
//
//        // Guardar los cambios en la base de datos
//        $entityManager = $this->getDoctrine()->getManager();
//        $entityManager->flush();
//
//        // Aquí deberías enviar el correo electrónico al usuario con la nueva contraseña
//
//        return $this->json(['message' => 'Se ha enviado una nueva contraseña al correo electrónico del usuario'], Response::HTTP_OK);
//    }
//
//    private function generarNuevaContrasena(): string
//    {
//        // Implementa la lógica para generar una nueva contraseña
//        // Puedes usar funciones de PHP como random_bytes() o cualquier otro método que prefieras
//        // Por ejemplo, aquí generamos una contraseña aleatoria de 10 caracteres
//        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10);
//    }
//
//    private function getDoctrine()
//    {
//    }


    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/recuperarpwd', name: "recuperarpwd", methods: ["POST"])]
    public function recuperarpwd(Request $request, UsuarioRepository $usuarioRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $json = json_decode($request->getContent(), true);

        // Buscar al usuario por nombre de usuario o correo electrónico
        $usuario = $usuarioRepository->findOneBy([
            'username' => $json['username'],
            // Agregar búsqueda por correo electrónico
            'email' => $json['email']
        ]);

        if (!$usuario) {
            return $this->json(['message' => 'Usuario no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Verificar si el usuario está verificado
        if (!$usuario->getIsVerified()) {
            return $this->json(['message' => 'Usuario no verificado'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Generar y enviar la nueva contraseña al usuario
        $nuevaContrasena = $this->generarNuevaContrasena(); // Implementa la lógica para generar una nueva contraseña
        $usuario->setPassword($passwordHasher->hashPassword($usuario, $nuevaContrasena)); // Asignar la nueva contraseña hasheada al usuario

        // Guardar los cambios en la base de datos
        $this->entityManager->flush();

        // Aquí deberías enviar el correo electrónico al usuario con la nueva contraseña

        return $this->json(['message' => 'Se ha enviado una nueva contraseña al correo electrónico del usuario'], JsonResponse::HTTP_OK);
    }

    private function generarNuevaContrasena(): string
    {
        // Implementa la lógica para generar una nueva contraseña
        // Puedes usar funciones de PHP como random_bytes() o cualquier otro método que prefieras
        // Por ejemplo, aquí generamos una contraseña aleatoria de 10 caracteres
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10);
    }

}