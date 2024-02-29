<?php

namespace App\Controller;

use App\Entity\Canal;
use App\Entity\TipoContenido;
use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use DiscordWebhook\Webhook;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;



#[Route('/api/registro')]
class RegistroController extends AbstractController
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;
    private $entityManager;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $entityManager )
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }





    #[Route('/registrar', name: 'registrar_usuario', methods: ['POST'])]
    public function register(
        Request                     $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface      $entityManager
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {

            // Crear un nuevo usuario
            $user = new Usuario();
            $user->setUsername($data['username']);
            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
            $user->setEmail($data['email']);
            $user->setWebhook($data['webhook']);

            // Generar el token de verificación y asociarlo al usuario
            $user->generateVerificationToken();

            //Setear el token de verificacion al usuario
//            $user->setVerificationToken($user->getVerificationToken());

//        // Guardar el usuario en la base de datos
//        $entityManager->persist($user);
//        $entityManager->flush();

            // Crear un nuevo canal asociado al usuario
            $canal = new Canal();
//        $canal->setEmail($data['email']); // Asegúrate de que el campo de correo electrónico esté presente en los datos
            $canal->setUsuario($user); // Asociar el canal al usuario
            $canal->setNombre($data['canal']["nombre"]);
            $canal->setApellidos($data['canal']["apellidos"]);
            $canal->setDescripcion($data['canal']["descripcion"]);
//        $canal->setFechaNacimiento(new \DateTime($data['fecha_nacimiento']));
            $canal->setFechaNacimiento($data['canal']["fecha_nacimiento"]);
            $canal->setTelefono($data['canal']["telefono"]);
            $canal->setFoto($data['canal']["foto"]);

            //buscar el tipo de contenido con el findby y setearlo
            $tipoContenido = $entityManager->getRepository(TipoContenido::class)->findOneBy(['id' => $data['canal']["tipo_contenido"]]);
            $canal->setTipoContenido($tipoContenido);
//        $canal->setTipoContenido($data['tipo_contenido']);

            $canal->setBanner($data['canal']["banner"]);

            //COMUNIDAD DISCORD DEL CANAL
//            $canal->setcomunidadDiscord($data['canal']["comunidad_discord"]);

            // $canal->setUsuario($user);

//        // Guardar el canal en la base de datos
//        $entityManager->persist($canal);
//        $entityManager->flush();

            // Guardar el usuario con el token de verificación actualizado en la BBDD
            $entityManager->persist($user);
//            $entityManager->flush();

            // Guardar el canal con el token de verificación actualizado en la BBDD
            $entityManager->persist($canal);

            // Flusheo de cambios a la BBDD
            $entityManager->flush();


            // Enviar correo de verificación
//            $this->sendVerificationEmail($user);

//            // Enviar el token de verificación por webhook (DISCORD)
            $this->sendVerificationToken($user);

            // Enviar correo de verificación
            $this->sendVerificationEmail($request, $entityManager, $this->mailer);


            // Devolver una respuesta JSON exitosa
            return new JsonResponse(['message' => 'Usuario registrado con éxito'], 201);
        } catch (UniqueConstraintViolationException $e) {
            // Capturar la excepción de violación de unicidad y devolver un mensaje de error
            return new JsonResponse(['error' => 'Este correo electrónico ya está en uso.'], 400);
        } catch (\Exception $e) {
            // Capturar otras excepciones y devolver un mensaje de error
            return new JsonResponse(['error' => 'Error al registrar el usuario'], 500);
        }
    }




//
//
//        // Guardar el usuario con el token de verificación actualizado
//        $entityManager->persist($user);
//        $entityManager->flush();
//
//        // Devolver una respuesta JSON exitosa
//        return new JsonResponse(['message' => 'Usuario verificado con éxito'], 200);
//    }

    #[Route('/enviar', name: 'enviar_verificacion', methods: ['POST'])]
//    private function sendVerificationEmail(Usuario $user): JsonResponse
//    {
//        $email = (new Email())
//            ->from('safatuberisk24@gmail.com')
//            ->to($user->getEmail())
//            ->subject('Verificación de Correo Electrónico')
//            ->html(
//                $this->renderView(
//                    'emails/verification.html.twig',
//                    ['token' => $user->getVerificationToken()]
//                )
//            );
    public function sendVerificationEmail(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $emailDestino = $data['email'];
        $user = $entityManager->getRepository(Usuario::class)->findOneBy(['email' => $emailDestino]);

        $email = (new Email())
            ->from('safatuberisk24@gmail.com')
            ->to($emailDestino)
            ->subject('Se ha registrado con éxito en SAFATUBE')
            ->html(
                $this->renderView(
                    'emails/verification.html.twig',
                    ['token' => $user->getVerificationToken()]
                )
            );
//            ->html('Registro SafaTube');
        try {
            $mailer->send($email);
        } catch (\Exception $e) {
            $this->logger->error('Error al enviar el correo de verificación: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Error al enviar el correo de verificación'], 500);
        }


        // Devolver una respuesta exitosa si va bien
        return new JsonResponse(['message' => 'Correo de verificación enviado con éxito'], 200);
    }

    #[Route('/reenviar', name: 'reenviar_verificacion', methods: ['POST'])]
    public function resendVerificationEmail(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $usuario = new Usuario();

        $data = json_decode($request->getContent(), true);

        // Buscar el usuario con el correo electrónico dado
        $user = $entityManager->getRepository(Usuario::class)->findOneBy(['email' => $data['email']]);

        // Verificar si el usuario existe y si no, devolver una respuesta JSON con un error
        if (!$user) {
            return new JsonResponse(['error' => 'No se ha encontrado ningún usuario con ese correo electrónico'], 400);
        }

        // Verificar si el usuario ya está verificado y si no, devolver una respuesta JSON con un error
        if ($user->getIsVerified()) {
            return new JsonResponse(['error' => 'Este usuario ya está verificado'], 400);
        }
//        // Guardar el usuario con el token de verificación actualizado
        $entityManager->persist($usuario);
        $entityManager->flush();

        // Enviar correo de verificación
//        $this->sendVerificationEmail($user['email'], $user['verification_token']);
        $this->sendVerificationEmail($request, $entityManager, $this->mailer);

        // Devolver una respuesta JSON exitosa
        return new JsonResponse(['message' => 'Usuario registrado con éxito'], 201);
    }

//    private function setVerificationToken(string $token): void
//    {
//        $this->verificationToken = $token;
//    }
    public function sendVerificationToken(Usuario $user)
    {
        $token = $user->getVerificationToken();
        $wh = new Webhook($user->getWebhook());
//        $wh->setMessage('Hola ' . $user->getUsername() . '¡Gracias por registrarte en SafaTube!. Para verificar tu cuenta, haz clic en el siguiente enlace: https://safatuber.herokuapp.com/api/registro/verificar/' . $token)->send();
       // $wh->setMessage('Hola ' . $user->getUsername() . '¡Gracias por registrarte en SafaTube!. Para verificar tu cuenta, haz clic en el siguiente enlace: https://safatuber.herokuapp.com/api/registro/verificar/' . $token)->send();

        //mensaje para verificar el token por webhook
        $wh->setMessage('Hola ' . $user->getUsername() . ' ¡Gracias por registrarte en SafaTube!. Para verificar tu cuenta, introduce el siguiente token de validación junto tu nombre de usuario y tu email. TOKEN: ' . $token)->send();
    }


//este verifica el usuario con (username, email y token) AL CARAJO VALIDA SOLO CON TOKEN
    #[Route('/verificar', name: 'verificar_usuario', methods: ['POST'])]
    public function verifyUser(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        // Buscar el usuario con el token de verificación dado
        $user = $entityManager->getRepository(Usuario::class)->findOneBy(['verification_token' => $data['token']]);

        // Verificar si el usuario existe y si no, devolver una respuesta JSON con un error
        if (!$user) {
            return new JsonResponse(['error' => 'Token de verificación inválido'], 404);
        } else {

            // Verificar si el usuario ya está verificado y si no, devolver una respuesta JSON con un error
            if ($user->getIsVerified()) {
                return new JsonResponse(['error' => 'Este usuario ya está verificado'], 400);
            } else {
//                //verificar si el token de verificacion coincide con el token de verificacion del usuario
//                if ($user->getVerificationToken() !== $token) {
//                    return new JsonResponse(['error' => 'Token de verificación inválido'], 400);
//                } else {
//                    //verificar si el email del usuario coincide con el email del usuario
//                    if ($user->getEmail() !== $user->getEmail()) {
//                        return new JsonResponse(['error' => 'Email asociado incorrecto'], 400);
//                    }
//                    else {
                // validar el usuario, token, email y canal de usuario

                $user->setIsVerified(true);

                // Validar el canal sociado al usuario
                $canal = $entityManager->getRepository(Canal::class)->findOneBy(['usuario' => $user]);
                $canal->setIsVerified(true);

                // Actualizar el token de verificación del usuario
//                $user->setVerificationToken(null);
//                $user->setCuentaValidada(true);
                // Actualizar el estado del canal asociado al usuario
                $canal = $entityManager->getRepository(Canal::class)->findOneBy(['usuario' => $user]);

                $entityManager->persist($user);
                $entityManager->flush();
                return new JsonResponse(['message' => 'Usuario verificado con éxito'], 201);
            }
//            return new JsonResponse(['success' => 'Usuario verificado con éxito'], 200);
        }
//    }
//    }
    }



    #[Route('/verificarmail/{token}', name: 'verificar_usuario_email', methods: ['POST'])]
    public function verifyEmailUser(string $token, EntityManagerInterface $entityManager): JsonResponse
    {
        // Buscar el usuario con el token de verificación dado
        $user = $entityManager->getRepository(Usuario::class)->findOneBy(['verification_token' => $token]);

        // Verificar si el usuario existe y si no, devolver una respuesta JSON con un error
        if (!$user) {
            return new JsonResponse(['error' => 'Token de verificación inválido'], 404);
        }

        // Verificar si el usuario ya está verificado y si no, marcarlo como verificado
        if (!$user->getIsVerified()) {
            // Marcar al usuario como verificado
            $user->setIsVerified(true);

            // Obtener y validar el canal asociado al usuario
            $canal = $entityManager->getRepository(Canal::class)->findOneBy(['usuario' => $user]);
            if ($canal) {
                $canal->setIsVerified(true);
            }

            // Persistir los cambios en la base de datos
            $entityManager->flush();

            return new JsonResponse(['message' => 'Usuario verificado con éxito'], 200);
        } else {
            // Si el usuario ya está verificado, devolver una respuesta JSON con un error
            return new JsonResponse(['error' => 'Este usuario ya está verificado'], 400);
        }
    }



    //RECUPERAR PWD
    #[Route('/recuperarpwd', name: "recuperarpwd", methods: ["POST"])]
    public function recuperarpwd(Request $request, entityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        // Buscar el usuario con el token de verificación dado

        $usuario = $entityManager->getRepository(Usuario::class)->findOneBy(['email' => $data['email']]);
//        $usuario = $entityManager->getRepository(Usuario::class)->findOneBy(['username' => $data['username']]);



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
        // Enviar el correo electrónico con la nueva contraseña al usuario
        $this->enviarCorreoPwd($usuario->getEmail(), $nuevaContrasena);


        return $this->json(['message' => 'Se ha enviado una nueva contraseña al correo electrónico del usuario'], JsonResponse::HTTP_OK);
    }

    private function enviarCorreoPwd(string $email, string $newPwd): void
    {
        $email = (new Email())
            ->from('safatuberisk24@gmail.com')
            ->to($email)
            ->subject('Recuperación de Contraseña')
            ->text("Tu nueva contraseña es: $newPwd");

        $this->mailer->send($email);
    }
    private function generarNuevaContrasena(): string
    {
        // Implementa la lógica para generar una nueva contraseña
        // Puedes usar funciones de PHP como random_bytes() o cualquier otro método que prefieras
        // Por ejemplo, aquí generamos una contraseña aleatoria de 10 caracteres
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10);
    }



    //metodo para editar la password
    #[Route('/editarpassword', name: 'editar_password', methods: ['POST'])]
    public function editarPassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Buscar el usuario con el token de verificación dado
        $usuario = $entityManager->getRepository(Usuario::class)->findOneBy(['email' => $data['email']]);

        if (!$usuario) {
            return new JsonResponse(['message' => 'Usuario no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Verificar si el usuario está verificado
        if (!$usuario->getIsVerified()) {
            return new JsonResponse(['message' => 'Usuario no verificado'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Verificar si la contraseña actual es correcta
        if (!$passwordHasher->isPasswordValid($usuario, $data['password'])) {
            return new JsonResponse(['message' => 'Contraseña incorrecta'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Asignar la nueva contraseña hasheada al usuario
        $usuario->setPassword($passwordHasher->hashPassword($usuario, $data['new_password']));

        // Guardar los cambios en la base de datos
        $entityManager->flush();

        return new JsonResponse(['message' => 'Contraseña actualizada con éxito'], JsonResponse::HTTP_OK);
    }


    //metodo para actualizar el password
//    #[Route('/updatepassword', name: 'actualizar_password', methods: ['POST'])]
//    public function updatePassword(Request $request): JsonResponse
//    {
//        // Obtiene los datos de la solicitud
//        $requestData = json_decode($request->getContent(), true);
//
//        // Obtiene el nuevo password de los datos de la solicitud
//        $newPassword = $requestData['newPassword'];
//
//        // Obtiene el usuario actual (suponiendo que estás utilizando algún sistema de autenticación)
//        $usuario = $this->getUser();
//
//        // Cambia la contraseña del usuario
//        $entityManager = $this->getUser() // Suponiendo que el método para obtener el EntityManager se llama getEntityManager
//        $usuario->setPassword($newPassword); // Suponiendo que el método para establecer la contraseña se llama setPassword
//        $entityManager->flush();
//
//        // Devuelve una respuesta exitosa
//        return new JsonResponse(['message' => 'Contraseña actualizada correctamente'], JsonResponse::HTTP_OK);
//    }
//

//    #[Route('/updatepassword', name: 'actualizar_password', methods: ['POST'])]
//    public function updatePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager): JsonResponse
//    {
//        // Decodificar los datos de la solicitud JSON
//        $requestData = json_decode($request->getContent(), true);
//
//        // Obtener el nuevo password del cuerpo de la solicitud
//        $newPassword = $requestData['newPassword'];
//
//        // Obtener el usuario actual
//        $usuario = $this->getUser();
//
//        // Verificar si el usuario actual está autenticado
//        if (!$usuario) {
//            return new JsonResponse(['error' => 'Usuario no autenticado'], JsonResponse::HTTP_UNAUTHORIZED);
//        }
//
//        try {
//            // Codificar el nuevo password
//            $encodedPassword = $passwordEncoder->encodePassword($usuario, $newPassword);
//
//            // Establecer el nuevo password para el usuario
//            $usuario->setPassword($encodedPassword);
//
//            // Guardar los cambios en la base de datos
//            $entityManager->flush();
//
//            // Devolver una respuesta exitosa
//            return new JsonResponse(['message' => 'Contraseña actualizada correctamente'], JsonResponse::HTTP_OK);
//        } catch (\Exception $e) {
//            // Capturar otras excepciones y devolver un mensaje de error
//            return new JsonResponse(['error' => 'Error al actualizar la contraseña'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
//        }
//    }


    #[Route('/updatepassword', name: 'actualizar_password', methods: ['POST'])]
    public function updatePassword(Request $request, TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager): JsonResponse
    {
        // Decodificar los datos de la solicitud JSON
        $requestData = json_decode($request->getContent(), true);

        // Obtener el nuevo password del cuerpo de la solicitud
        $newPassword = $requestData['newPassword'];

        // Obtener el usuario actual
        $usuario = $tokenStorage->getToken()->getUser();

        // Verificar si el usuario actual está autenticado
        if (!$usuario) {
            return new JsonResponse(['error' => 'Usuario no autenticado'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            // Codificar el nuevo password
            $encodedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Establecer el nuevo password para el usuario
            $usuario->setPassword($encodedPassword);

            // Guardar los cambios en la base de datos
            $entityManager->flush();

            // Devolver una respuesta exitosa
            return new JsonResponse(['message' => 'Contraseña actualizada correctamente'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            // Capturar otras excepciones y devolver un mensaje de error
            return new JsonResponse(['error' => 'Error al actualizar la contraseña'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //metodo para coger el webhook de usuario y enviarlo al front
    #[Route('/getwebhook', name: 'get_webhook', methods: ['POST'])]
    public function getWebhook(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->getContent();

        // Buscar el usuario con el token de verificación dado
        $usuario = $entityManager->getRepository(Usuario::class)->findOneBy(['username' => $data]);

        if (!$usuario) {
            return new JsonResponse(['message' => 'Usuario no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Verificar si el usuario está verificado
        if (!$usuario->getIsVerified()) {
            return new JsonResponse(['message' => 'Usuario no verificado'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Devolver el webhook del usuario
        return new JsonResponse(['webhook' => $usuario->getWebhook()], JsonResponse::HTTP_OK);
    }

}
