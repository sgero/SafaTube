<?php

namespace App\Entity;

use App\Repository\CanalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CanalRepository::class)]
#[ORM\Table(name: "canal", schema: "safatuber24")]
class Canal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 400)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 30)]
    private ?string $nombre = null;

    #[ORM\Column(length: 30)]
    private ?string $apellidos = null;

    #[ORM\Column(length: 200)]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha_nacimiento = null;

    #[ORM\Column(length: 12)]
    private ?string $telefono = null;

    #[ORM\Column(length: 5000)]
    private ?string $foto = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, name: 'id_usuario')]
    private ?Usuario $usuario = null;

    #[ORM\OneToMany(mappedBy: 'canal', targetEntity: Notificacion::class, orphanRemoval: true)]
    private Collection $notificacions;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "id_tipo_contenido")]
    private ?TipoContenido $tipoContenido = null;

    public function __construct()
    {
        $this->notificacions = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): static
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFechaNacimiento(): ?\DateTimeInterface
    {
        return $this->fecha_nacimiento;
    }

    public function setFechaNacimiento(\DateTimeInterface $fecha_nacimiento): static
    {
        $this->fecha_nacimiento = $fecha_nacimiento;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): static
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(string $foto): static
    {
        $this->foto = $foto;

        return $this;
    }


    public function getUsuario(): ?usuario
    {
        return $this->usuario;
    }

    public function setUsuario(usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * @return Collection<int, Notificacion>
     */
    public function getNotificacions(): Collection
    {
        return $this->notificacions;
    }

    public function addNotificacion(Notificacion $notificacion): static
    {
        if (!$this->notificacions->contains($notificacion)) {
            $this->notificacions->add($notificacion);
            $notificacion->setIdCanal($this);
        }

        return $this;
    }

    public function removeNotificacion(Notificacion $notificacion): static
    {
        if ($this->notificacions->removeElement($notificacion)) {
            // set the owning side to null (unless already changed)
            if ($notificacion->getIdCanal() === $this) {
                $notificacion->setIdCanal(null);
            }
        }

        return $this;
    }

    public function getTipoContenido(): ?TipoContenido
    {
        return $this->tipoContenido;
    }

    public function setTipoContenido(?TipoContenido $tipoContenido): static
    {
        $this->tipoContenido = $tipoContenido;

        return $this;
    }

}
