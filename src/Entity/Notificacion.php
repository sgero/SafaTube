<?php

namespace App\Entity;

use App\Enum\TipoNotificacion;
use App\Repository\NotificacionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificacionRepository::class)]
class Notificacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column()]
    private ?TipoNotificacion $tipoNotificacion = null;
    #[ORM\Column(length: 200)]
    private ?string $mensaje = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\ManyToOne(inversedBy: 'notificacions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Canal $id_canal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipoNotificacion(): ?TipoNotificacion
    {
        return $this->tipoNotificacion;
    }

    public function setTipoNotificacion(TipoNotificacion $tipoNotificacion): static
    {
        $this->tipoNotificacion = $tipoNotificacion;

        return $this;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function setMensaje(string $mensaje): static
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

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
