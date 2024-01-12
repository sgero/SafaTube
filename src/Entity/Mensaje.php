<?php

namespace App\Entity;

use App\Repository\MensajeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MensajeRepository::class)]
class Mensaje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    private ?string $texto = null;

    #[ORM\Column]
    private ?int $id_usuario_emisor = null;

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

    public function getIdUsuarioEmisor(): ?int
    {
        return $this->id_usuario_emisor;
    }

    public function setIdUsuarioEmisor(int $id_usuario_emisor): static
    {
        $this->id_usuario_emisor = $id_usuario_emisor;

        return $this;
    }
}
