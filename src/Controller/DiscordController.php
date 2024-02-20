<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use DiscordWebhook\Webhook;

#[Route('/api/discord')]
class DiscordController extends AbstractController
{

    #[Route('', name: 'app_discord', methods: ['GET'])]
    public function index(): Response
    {
        $wh = new Webhook('https://discordapp.com/api/webhooks/1207396351235727360/KBeedzcag_jZQ7tv_1FnfnnWVP1vf5dhkGqbbbL9CZp0hoFa6djWA0RZyQ9cemEQG8Jm');
        $wh->setMessage('Hello world!')->send();

        return new Response('Mensaje Enviado!');
    }
}

