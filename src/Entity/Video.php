<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
#[ORM\Table(name: "video", schema: "safatuber24")]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\Column(length: 1000)]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column]
    private ?int $duracion = null;

    #[ORM\Column]
    private ?bool $activo = true;

    #[ORM\ManyToOne(cascade: ['remove', 'persist'])]
    #[ORM\JoinColumn(nullable: false, name: "id_canal")]
    private ?Canal $canal = null;

    #[ORM\Column(length: 10000)]
    private ?string $enlace = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "id_tipo_categoria")]
    private ?TipoCategoria $tipoCategoria = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "id_tipo_privacidad")]
    private ?TipoPrivacidad $tipoPrivacidad = null;

    #[ORM\Column (name: "total_visitas")]
    private ?int $totalVisitas = 0;

    #[ORM\ManyToMany(targetEntity: Usuario::class)]
    #[ORM\JoinTable(name: "visualizacion_video_usuario", schema: "safatuber24")]
    #[ORM\JoinColumn(name: "id_video", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "id_usuario", referencedColumnName: "id")]
    private Collection $visualizaciones;
    public function getVisualizaciones(): Collection
    {
        return $this->visualizaciones;
    }

    public function addVisualizaciones(Usuario $usuario): static
    {
        if (!$this->visualizaciones->contains($usuario)) {
            $this->visualizaciones->add($usuario);
        }

        return $this;
    }

    public function removeVisualizaciones(Usuario $usuario): static
    {
        $this->visualizaciones->removeElement($usuario);

        return $this;
    }
    public function __construct()
    {
        $this->visualizaciones = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getFecha(): ?string
    {
        return $this->fecha->format('d/m/Y');
    }

    public function setFecha(string $fecha): static
    {
        $this->fecha = \DateTime::createFromFormat('d/m/Y',$fecha);

        return $this;
    }


    public function getDuracion(): ?int
    {
        return $this->duracion;
    }

    public function setDuracion(int $duracion): static
    {
        $this->duracion = $duracion;

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

    public function getCanal(): ?Canal
    {
        return $this->canal;
    }

    public function setCanal(?Canal $canal): static
    {
        $this->canal = $canal;

        return $this;
    }

    public function getEnlace(): ?string
    {
        return $this->enlace;
    }

    public function setEnlace(string $enlace): static
    {
        $this->enlace = $enlace;

        return $this;
    }

    public function getTipoCategoria(): ?TipoCategoria
    {
        return $this->tipoCategoria;
    }

    public function setTipoCategoria(?TipoCategoria $tipoCategoria): static
    {
        $this->tipoCategoria = $tipoCategoria;

        return $this;
    }

    public function getTipoPrivacidad(): ?TipoPrivacidad
    {
        return $this->tipoPrivacidad;
    }

    public function setTipoPrivacidad(?TipoPrivacidad $tipoPrivacidad): static
    {
        $this->tipoPrivacidad = $tipoPrivacidad;

        return $this;
    }

    public function getTotalVisitas(): ?int
    {
        return $this->totalVisitas;
    }

    public function setTotalVisitas(int $totalVisitas): static
    {
        $this->totalVisitas = $totalVisitas;

        return $this;
    }


}
