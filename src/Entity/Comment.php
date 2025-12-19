<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource]
class Comment
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    private string $id;

    #[ORM\Column(type: "text")]
    private string $content;

    #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy: "comments")]
    #[ORM\JoinColumn(nullable: false)]
    private Ticket $ticket;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    use \App\Entity\Traits\TimestampableTrait;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
    }

    public function getId(): string { return $this->id; }

    public function getContent(): string { return $this->content; }
    public function setContent(string $c): self { $this->content = $c; return $this; }

    public function getTicket(): Ticket { return $this->ticket; }
    public function setTicket(Ticket $t): self { $this->ticket = $t; return $this; }

    public function getAuthor(): User { return $this->author; }
    public function setAuthor(User $u): self { $this->author = $u; return $this; }
}
