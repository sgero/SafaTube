<?php

namespace App\Enum;

enum TipoNotificacion: string{

    case subscripcion = 'SUBSCRIPCION';

    case like = 'LIKE';

    case dislike = 'DISLIKE';

    case nuevoVideo = 'NUEVOVIDEO';

    case mensaje = 'MENSAJE';

    case comentario = 'COMENTARIO';

}