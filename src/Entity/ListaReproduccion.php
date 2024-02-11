<?php

namespace App\Entity;

use App\Repository\ListaReproduccionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListaReproduccionRepository::class)]
#[ORM\Table(name: "lista_reproduccion", schema: "safatuber24")]
class ListaReproduccion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "id_canal")]
    private ?Canal $canal = null;

    #[ORM\ManyToMany(targetEntity: Video::class)]
    #[ORM\JoinTable(name: "video_lista_reproduccion", schema: "safatuber24")]
    #[ORM\JoinColumn(name: "id_lista_reproduccion", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "id_video", referencedColumnName: "id")]
    private Collection $videos;

    public function __construct()
    {
        $this->videos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getCanal(): ?Canal
    {
        return $this->canal;
    }

    public function setCanal(?Canal $canal): static
    {
        $this->canal = $canal;

        return $this;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideos(Video $videos): static
    {
        if (!$this->videos->contains($videos)) {
            $this->videos->add($videos);
        }

        return $this;
    }

    public function removeVideos(Video $videos): static
    {
        $this->videos->removeElement($videos);

        return $this;
    }
}
