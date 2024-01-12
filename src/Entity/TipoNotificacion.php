<?php

namespace App\Entity;

use App\Repository\TipoNotificacionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TipoNotificacionRepository::class)]
#[ORM\Table(name: "tipo_notificacion", schema: "safatuber24")]
class TipoNotificacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Notificacion $id_notificacion = null;




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

    public function getIdNotificacion(): ?Notificacion
    {
        return $this->id_notificacion;
    }

    public function setIdNotificacion(?Notificacion $id_notificacion): static
    {
        $this->id_notificacion = $id_notificacion;

        return $this;
    }



}