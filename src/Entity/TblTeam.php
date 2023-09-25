<?php

namespace App\Entity;

use App\Repository\TblTeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TblTeamRepository::class)]
class TblTeam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $matches_played = null;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: TblPlayer::class)]
    private Collection $tblPlayers;

    public function __construct()
    {
        $this->tblPlayers = new ArrayCollection();
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
     * @return Collection<int, TblPlayer>
     */
    public function getTblPlayers(): Collection
    {
        return $this->tblPlayers;
    }

    public function addTblPlayer(TblPlayer $tblPlayer): static
    {
        if (!$this->tblPlayers->contains($tblPlayer)) {
            $this->tblPlayers->add($tblPlayer);
            $tblPlayer->setTeam($this);
        }

        return $this;
    }

    public function removeTblPlayer(TblPlayer $tblPlayer): static
    {
        if ($this->tblPlayers->removeElement($tblPlayer)) {
            // set the owning side to null (unless already changed)
            if ($tblPlayer->getTeam() === $this) {
                $tblPlayer->setTeam(null);
            }
        }

        return $this;
    }
}
