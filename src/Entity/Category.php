<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource]
class Category
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    private string $id;

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: "category")]
    private Collection $tickets;

    use \App\Entity\Traits\TimestampableTrait;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->tickets = new ArrayCollection();
    }

    public function getId(): string { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $n): self { $this->name = $n; return $this; }

    public function getTickets(): Collection { return $this->tickets; }
}
