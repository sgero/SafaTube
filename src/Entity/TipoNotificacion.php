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
    #[ORM\JoinColumn(nullable: false, name: "id_notificacion")]
    private ?Notificacion $notificacion = null;




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
        return $this->notificacion;
    }

    public function setIdNotificacion(?Notificacion $notificacion): static
    {
        $this->notificacion = $notificacion;

        return $this;
    }



}
