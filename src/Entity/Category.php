<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TeamRepository;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'There is already a category with this name')]
#[ORM\Table(name: 'tbl_category')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'Category', targetEntity: Gathering::class)]
    private Collection $gatherings;

    #[ORM\OneToMany(mappedBy: 'Category', targetEntity: User::class)]
    private Collection $users;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'team_id', referencedColumnName: 'id')]
    private $team;

    #[ORM\Column(length: 255)]
    private ?string $color = null;
    
    public function __construct()
    {
        $this->gatherings = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Gathering>
     */
    public function getGatherings(): Collection
    {
        return $this->gatherings;
    }

    public function addGathering(Gathering $gathering): static
    {
        if (!$this->gatherings->contains($gathering)) {
            $this->gatherings->add($gathering);
            $gathering->setCategory($this);
        }

        return $this;
    }

    public function removeGathering(Gathering $gathering): static
    {
        if ($this->gatherings->removeElement($gathering)) {
            // set the owning side to null (unless already changed)
            if ($gathering->getCategory() === $this) {
                $gathering->setCategory(null);
            }
        }

        return $this;
    }
    
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCategory($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCategory() === $this) {
                $user->setCategory(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }
}
