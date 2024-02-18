<?php

namespace App\Controller;

use App\Entity\Canal;
use App\Entity\TipoContenido;
use App\Entity\Usuario;
use DiscordWebhook\Webhook;
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

#[Route('/api/registro')]
class RegistroController extends AbstractController
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
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

    #[Route('/verificar/{token}', name: 'verificar_usuario', methods: ['GET'])]
    public function verifyUser(string $token, EntityManagerInterface $entityManager): JsonResponse
    {
        // Buscar el usuario con el token de verificación dado
        $user = $entityManager->getRepository(Usuario::class)->findOneBy(['verification_token' => $token]);

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
        $canal = new Canal();
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
//        // Guardar el canal con el token de verificación actualizado
        $entityManager->persist($canal);
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
        $wh->setMessage('Hola ' . $user->getUsername() . '¡Gracias por registrarte en SafaTube!. Para verificar tu cuenta, introduce el siguiente token de validación junto tu nombre de usuario y tu email. TOKEN: ' . $token)->send();
    }


}
