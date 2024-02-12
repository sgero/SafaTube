<?php

namespace App\DTO;

use App\Entity\Video;
use Doctrine\Common\Collections\ArrayCollection;
use phpDocumentor\Reflection\Types\Collection;

class FiltroDTO
{
    private ArrayCollection $videos;
    private ArrayCollection $canales;

    public function getVideos(): ArrayCollection
    {
        return $this->videos;
    }

    public function setVideos(ArrayCollection $videos): void
    {
        $this->videos = $videos;
    }

    public function getCanales(): ArrayCollection
    {
        return $this->canales;
    }

    public function setCanales(ArrayCollection $canales): void
    {
        $this->canales = $canales;
    }






}