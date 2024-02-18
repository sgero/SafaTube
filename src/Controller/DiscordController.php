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


//TODO: falta en BBDD crear el campo Webhook con la URL de cada usuario

//TODO: falta hacer el metodo para que cuando uno se registra te envie el mail de validacion con el token, y te redirija a la pantalla de validacion de la cuenta, en la que tendrás que meter el mail y el token

//TODO: si coincide saldra un toaster con usuario verificado con exito y te redirigira a la pantalla de login.

//TODO: los usuarios verificados si podrán loguearse, los no verificados no.

//TODO: los canales automaticamente se pondrán como verificados y si podran ser listados en la pantalla de canales.

//TODO: RECUPERAR CONTRASEÑA. para recuperar contraseña se introducirà el email del usuario, y hará una llamada del usuario y su token de verificación se enviará por webhook. Acto seguido se abrirá una pantalla para introducir el token y el nombre de usuario asociada al mail, introduciendo ese token de verificacion junto al nombre de usuario, se abrira un modal para cambiar la contraseña.

//TODO: Redirección a login para loguearse con el nuevo username y password...