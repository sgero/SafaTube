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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "id_usuario_emisor")]
    private ?Usuario $usuario_emisor = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "id_usuario_receptor")]
    private ?Usuario $usuario_receptor = null;

    #[ORM\Column]
    private ?bool $leido = false;

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

    public function getFecha(): ?string
    {
        return $this->fecha->format('d/m/Y H:i:s');
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getUsuarioEmisor(): ?Usuario
    {
        return $this->usuario_emisor;
    }

    public function setUsuarioEmisor(?Usuario $usuario_emisor): static
    {
        $this->usuario_emisor = $usuario_emisor;

        return $this;
    }

    public function getUsuarioReceptor(): ?Usuario
    {
        return $this->usuario_receptor;
    }

    public function setUsuarioReceptor(?Usuario $usuario_receptor): static
    {
        $this->usuario_receptor = $usuario_receptor;

        return $this;
    }

    public function isLeido(): ?bool
    {
        return $this->leido;
    }

    public function setLeido(bool $leido): static
    {
        $this->leido = $leido;

        return $this;
    }
}
