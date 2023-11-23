<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\Table(name: 'tbl_team')]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $matches_played = null;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: Player::class)]
    private Collection $Players;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: User::class)]
    private Collection $users;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'team_id', referencedColumnName: 'id')]
    private $team;
    
    public function __construct()
    {
        $this->Players = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
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

    public function getMatchesPlayed(): ?int
    {
        return $this->matches_played;
    }

    public function setMatchesPlayed(int $matches_played): static
    {
        $this->matches_played = $matches_played;

        return $this;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->Players;
    }

    public function addPlayer(Player $Player): static
    {
        if (!$this->Players->contains($Player)) {
            $this->Players->add($Player);
            $Player->setTeam($this);
        }

        return $this;
    }

    public function removePlayer(Player $Player): static
    {
        if ($this->Players->removeElement($Player)) {
            // set the owning side to null (unless already changed)
            if ($Player->getTeam() === $this) {
                $Player->setTeam(null);
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
            $user->setTeam($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getTeam() === $this) {
                $user->setTeam(null);
            }
        }

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
}
