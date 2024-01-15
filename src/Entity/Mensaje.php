<?php

namespace App\Entity;

use App\Repository\MensajeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MensajeRepository::class)]
#[ORM\Table(name: "mensaje", schema: "safatuber24")]
class Mensaje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    private ?string $texto = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "id_usuario_emisor")]
    private ?Usuario $id_usuario_emisor = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "id_canal_receptor")]
    private ?Canal $id_canal_receptor = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(string $texto): static
    {
        $this->texto = $texto;

        return $this;
    }

    public function getIdUsuarioEmisor(): ?Usuario
    {
        return $this->id_usuario_emisor;
    }

    public function setIdUsuarioEmisor(?Usuario $id_usuario_emisor): static
    {
        $this->id_usuario_emisor = $id_usuario_emisor;

        return $this;
    }

    public function getIdCanalReceptor(): ?Canal
    {
        return $this->id_canal_receptor;
    }

    public function setIdCanalReceptor(?Canal $id_canal_receptor): static
    {
        $this->id_canal_receptor = $id_canal_receptor;

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
}
