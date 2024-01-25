<?php

namespace App\Entity;

use App\Repository\ComentarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComentarioRepository::class)]
#[ORM\Table(name: "comentario", schema: "safatuber24")]
class Comentario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 400)]
    private ?string $texto = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column]
    private ?bool $activo = true;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "id_video")]
    private ?Video $video = null;

    #[ORM\Column(name: "contador_likes")]
    private ?int $contadorLikes = null;

    #[ORM\Column(name: "contador_dislikes")]
    private ?int $contadorDislikes = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'comentarioPadre')]
    #[ORM\JoinColumn(name:"id_comentario_padre", referencedColumnName:"id", nullable:true)]
    private $comentarioPadre;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "id_usuario")]
    private ?Usuario $usuario = null;

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

    public function setFecha(string $fecha): static
    {
        $this->fecha = \DateTime::createFromFormat($fecha, $fecha);

        return $this;
    }

    public function isActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): static
    {
        $this->activo = $activo;

        return $this;
    }

    public function getIdVideo(): ?Video
    {
        return $this->video;
    }

    public function setIdVideo(?Video $video): static
    {
        $this->video = $video;

        return $this;
    }

    public function getContadorLikes(): ?int
    {
        return $this->contadorLikes;
    }

    public function setContadorLikes(int $contadorLikes): static
    {
        $this->contadorLikes = $contadorLikes;

        return $this;
    }

    public function getContadorDislikes(): ?int
    {
        return $this->contadorDislikes;
    }

    public function setContadorDislikes(int $contadorDislikes): static
    {
        $this->contadorDislikes = $contadorDislikes;

        return $this;
    }

    public function getComentarioPadre(): ?self
    {
        return $this->comentarioPadre;
    }

    public function setComentarioPadre(?self $comentarioPadre): static
    {
        $this->comentarioPadre = $comentarioPadre;

        return $this;
    }

    public function addComentarioPadre(self $comentarioPadre): static
    {
        if (!$this->comentarioPadre->contains($comentarioPadre)) {
            $this->comentarioPadre->add($comentarioPadre);
            $comentarioPadre->setComentarioPadre($this);
        }

        return $this;
    }

    public function removeComentarioPadre(self $comentarioPadre): static
    {
        if ($this->comentarioPadre->removeElement($comentarioPadre)) {
            // set the owning side to null (unless already changed)
            if ($comentarioPadre->getComentarioPadre() === $this) {
                $comentarioPadre->setComentarioPadre(null);
            }
        }

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }
}
