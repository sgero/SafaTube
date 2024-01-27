<?php

namespace App\Controller;

use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class SendEmailController extends AbstractController
{
    private $mailer;
    private $logger;

    #[Route('/send/email', name: 'app_send_email')]
    public function sendMail(MailerInterface $mailer): Response
    {

        try {
            $email = (new Email())
                ->from('safatuberisk24@gmail.com')
            //    ->to('$user->getEmail()')
                ->to('sgarciaguerrero@safareyes.es')
                ->subject('Verifica tu cuenta')
                ->text('Hola, por favor verifica tu cuenta')
                ->html('<p>Hola, por favor verifica tu cuenta</p>');

            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Error al enviar el correo electrónico: ' . $e->getMessage());
        }

        return $this->json(['message' => 'Email enviado'], Response::HTTP_OK);
    }

}
