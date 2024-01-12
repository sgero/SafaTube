<?php

namespace App\Entity;

use App\Repository\TipoContenidoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TipoContenidoRepository::class)]
#[ORM\Table(name: "tipo_contenido", schema: "safatuber24")]
class TipoContenido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Canal $id_canal = null;



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

    public function getIdCanal(): ?Canal
    {
        return $this->id_canal;
    }

    public function setIdCanal(?Canal $id_canal): static
    {
        $this->id_canal = $id_canal;

        return $this;
    }


}
