<?php

namespace App\Entity;

use App\Repository\TipoPrivacidadRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TipoPrivacidadRepository::class)]
#[ORM\Table(name: "tipo_privacidad", schema: "safatuber24")]
class TipoPrivacidad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nombre = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Video $id_video = null;

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

    public function getIdVideo(): ?Video
    {
        return $this->id_video;
    }

    public function setIdVideo(?Video $id_video): static
    {
        $this->id_video = $id_video;

        return $this;
    }
}
