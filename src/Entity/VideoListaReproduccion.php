<?php

namespace App\Entity;

use App\Repository\VideoListaReproduccionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoListaReproduccionRepository::class)]
class VideoListaReproduccion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Video::class)]
    private Collection $video;

    #[ORM\ManyToMany(targetEntity: ListaReproduccion::class)]
    private Collection $listaReproduccion;

    public function __construct()
    {
        $this->video = new ArrayCollection();
        $this->listaReproduccion = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideo(): Collection
    {
        return $this->video;
    }

    public function addVideo(Video $video): static
    {
        if (!$this->video->contains($video)) {
            $this->video->add($video);
        }

        return $this;
    }

    public function removeVideo(Video $video): static
    {
        $this->video->removeElement($video);

        return $this;
    }

    /**
     * @return Collection<int, ListaReproduccion>
     */
    public function getListaReproduccion(): Collection
    {
        return $this->listaReproduccion;
    }

    public function addListaReproduccion(ListaReproduccion $listaReproduccion): static
    {
        if (!$this->listaReproduccion->contains($listaReproduccion)) {
            $this->listaReproduccion->add($listaReproduccion);
        }

        return $this;
    }

    public function removeListaReproduccion(ListaReproduccion $listaReproduccion): static
    {
        $this->listaReproduccion->removeElement($listaReproduccion);

        return $this;
    }
}
