<?php

namespace App\Entity;

use App\Repository\GatheringRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GatheringRepository::class)]
#[ORM\Table(name: 'tbl_gathering')]
class Gathering
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $gathering_date = null;

    #[ORM\ManyToOne(inversedBy: 'gatherings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $Category = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'gatherings')]
    private Collection $Players;

    public function __construct()
    {
        $this->Players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGatheringDate(): ?\DateTimeInterface
    {
        return $this->gathering_date;
    }

    public function setGatheringDate(\DateTimeInterface $gathering_date): static
    {
        $this->gathering_date = $gathering_date;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->Category;
    }

    public function setCategory(?Category $Category): static
    {
        $this->Category = $Category;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getPlayers(): Collection
    {
        return $this->Players;
    }

    public function addPlayer(User $player): static
    {
        if (!$this->Players->contains($player)) {
            $this->Players->add($player);
        }

        return $this;
    }

    public function removePlayer(User $player): static
    {
        $this->Players->removeElement($player);

        return $this;
    }
}
