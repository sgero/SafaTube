<?php

namespace App\Enum;

enum TipoContenido: string{
    case Infantil = 'Infantil';
    case Gameplays = 'Gameplays';
    case Vlogs = 'Vlogs';
    case Tutoriales = 'Tutoriales';
    case Hauls = 'Hauls';
    case ProductReviews = 'Product Reviews';
    case Unboxing = 'Unboxing';
    case VideosFormativos = 'Vídeos formativos';
    case Podcasts = 'Podcasts';
    case Musica = 'Musica';
}
