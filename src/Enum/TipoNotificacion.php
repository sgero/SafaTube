<?php

namespace App\Enum;

enum TipoNotificacion: string{

    case suscripcion = 'SUSCRIPCION';

    case like = 'LIKE';

    case dislike = 'DISLIKE';

    case nuevoVideo = 'NUEVOVIDEO';

    case mensaje = 'MENSAJE';

    case comentario = 'COMENTARIO';

}