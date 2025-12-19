<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(),
        new Get(),
        new Put(),
        new Delete()
    ]
)]
class Ticket
{
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';

    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    private string $id;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(length: 20)]
    private string $priority = self::PRIORITY_NORMAL;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_OPEN;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $client;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $agent = null;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: "ticket", targetEntity: Comment::class, cascade: ["persist","remove"])]
    private Collection $comments;

    use \App\Entity\Traits\TimestampableTrait;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->comments = new ArrayCollection();
    }

    public function getId(): string { return $this->id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $desc): self { $this->description = $desc; return $this; }

    public function getPriority(): string { return $this->priority; }
    public function setPriority(string $p): self { $this->priority = $p; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $s): self { $this->status = $s; return $this; }

    public function getClient(): User { return $this->client; }
    public function setClient(User $u): self { $this->client = $u; return $this; }

    public function getAgent(): ?User { return $this->agent; }
    public function setAgent(?User $u): self { $this->agent = $u; return $this; }

    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $c): self { $this->category = $c; return $this; }

    /** @return Collection|Comment[] */
    public function getComments(): Collection { return $this->comments; }

    public function addComment(Comment $c): self
    {
        if (!$this->comments->contains($c)) {
            $this->comments[] = $c;
            $c->setTicket($this);
        }
        return $this;
    }

    public function removeComment(Comment $c): self
    {
        if ($this->comments->removeElement($c)) {
            if ($c->getTicket() === $this) {
                $c->setTicket(null);
            }
        }
        return $this;
    }
}
