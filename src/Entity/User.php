<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiProperty;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(), // GET /api/users
        new Get(),           // GET /api/users/{id}
        new Post()           // POST /api/users
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[ApiProperty(readable: true, writable: false)]
    #[Assert\NotBlank(message: "L'email ne peut pas être vide.")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide.")]
    private string $email;

    #[ORM\Column]
    #[Ignore]
    #[Assert\NotBlank(message: "Le mot de passe ne peut pas être vide.")]
    #[Assert\Length(min: 6, minMessage: "Le mot de passe doit contenir au moins 6 caractères.")]
    private string $password;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le rôle ne peut pas être vide.")]
    #[Assert\Choice(choices: ['client', 'agent', 'admin'], message: "Le rôle doit être 'client', 'agent' ou 'admin'.")]
    private string $role; // 'client', 'agent', 'admin'

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Ticket::class)]
    private Collection $ticketsCreated;

    #[ORM\OneToMany(mappedBy: 'agent', targetEntity: Ticket::class)]
    private Collection $ticketsAssigned;

    use \App\Entity\Traits\TimestampableTrait;

    public function __construct()
    {
        $this->ticketsCreated = new ArrayCollection();
        $this->ticketsAssigned = new ArrayCollection();
    }

    // --- Getters / Setters ---
    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getIdAsString(): ?string
    {
        return $this->id?->toRfc4122();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $e): self
    {
        $this->email = $e;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $p): self
    {
        $this->password = $p;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $r): self
    {
        $this->role = $r;
        return $this;
    }

    public function getTicketsCreated(): Collection
    {
        return $this->ticketsCreated;
    }

    public function getTicketsAssigned(): Collection
    {
        return $this->ticketsAssigned;
    }

    // --- Méthodes obligatoires pour Symfony Security ---
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        // Symfony attend un tableau de rôles
        return [$this->role];
    }

    public function eraseCredentials(): void
    {
        // Rien à faire ici
    }
}
