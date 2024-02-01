<?php

namespace App\Controller;

use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class SendEmailController extends AbstractController
{
    private $mailer;
    private $logger;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    #[Route('/send/email', name: 'app_send_email')]
    public function sendMail(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        // Validar que $data tiene las claves 'username' y 'email'
        if (!isset($data['username']) || !isset($data['email'])) {
            return $this->json(['error' => 'Datos incompletos'], Response::HTTP_BAD_REQUEST);
        }

        $user = new Usuario();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);

        try {
            $email = (new Email())
                ->from('safatuberisk24@gmail.com')
                ->to($user->getEmail())
                ->subject('Verifica tu cuenta')
                ->text('Hola, por favor verifica tu cuenta')
                ->html('<p>Hola, por favor verifica tu cuenta</p>');

            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Error al enviar el correo electrónico: ' . $e->getMessage());
            return $this->json(['error' => 'Error al enviar el correo electrónico'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['message' => 'Email enviado'], Response::HTTP_OK);
    }
}
