<?php

namespace App\Entity;

use App\Repository\NotificacionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificacionRepository::class)]
#[ORM\Table(name: "notificacion", schema: "safatuber24")]
class Notificacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 200)]
    private ?string $mensaje = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\ManyToOne(inversedBy: 'notificacions')]
    #[ORM\JoinColumn(nullable: false,name: 'id_canal')]
    private ?Canal $canal = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "id_tipo_notificacion")]
    private ?TipoNotificacion $tipoNotificacion = null;

    #[ORM\Column]
    private ?bool $atendida = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFecha(): ?string
    {
        return $this->fecha->format('d/m/Y H:i:s');
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

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

    public function getTipoNotificacion(): ?TipoNotificacion
    {
        return $this->tipoNotificacion;
    }

    public function setTipoNotificacion(?TipoNotificacion $tipoNotificacion): static
    {
        $this->tipoNotificacion = $tipoNotificacion;

        return $this;
    }

    public function isAtendida(): ?bool
    {
        return $this->atendida;
    }

    public function setAtendida(bool $atendida): static
    {
        $this->atendida = $atendida;

        return $this;
    }






}
