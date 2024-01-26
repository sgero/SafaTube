<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[ORM\Table(name: "usuario", schema: "safatuber24")]
class Usuario implements UserInterface,PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    private ?string $username = null;

    #[ORM\Column(length: 30)]
    private ?string $password = null;

    #[ORM\Column(name: 'es_admin')]
    private ?bool $admin = false;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length:255, nullable:true)]
    private ?string $verification_token = null;

//    #[ORM\Column(length: 255, nullable: true)]
//    private ?string $resetPasswordToken = null;
//
//    #[ORM\OneToOne(targetEntity: Canal::class, mappedBy: 'usuario', cascade: ['persist', 'remove'])]
//    private ?Canal $canal = null;
//
//    #[ORM\Column(type: 'datetime', nullable: true)]
//    private ?\DateTimeInterface $resetPasswordTokenExpiresAt = null;
//
//    #[ORM\Column(length: 255, nullable: true)]
//    private ?string $verificationToken = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): static
    {
        $this->admin = $admin;

        return $this;
    }
    public function getRoles(): array
    {
        $roles = [];
        return  $roles;
    }
    public function getUserIdentifier(): string
    {
        return $this-> getUsername();
    }
    public function eraseCredentials(): void{}

//    public function getEmail(): ?string
//    {
//        return $this->email;
//    }
//
//    public function setEmail(string $email): static
//    {
//        $this->email = $email;
//
//        return $this;
//    }

//
//    public function getCanal(): ?Canal
//    {
//        return $this->canal;
//    }
//
    public function generateVerificationToken(): void
    {
        $this->verification_token = bin2hex(random_bytes(32));
    }

    public function getVerificationToken(): ?string
    {
        return $this->verification_token;
    }

//    public function setVerificationToken(?string $verification_token): static
//    {
//        $this->verification_token = $verification_token;
//
//        return $this;
//    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }



}
