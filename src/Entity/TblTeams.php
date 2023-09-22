<?php

namespace App\Entity;

use App\Repository\TblTeamsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TblTeamsRepository::class)]
class TblTeams
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: TblPlayers::class)]
    private Collection $tblPlayers;

    #[ORM\Column]
    private ?int $matches_played = null;

    #[ORM\OneToMany(mappedBy: 'team_matches_played', targetEntity: TblPlayers::class)]
    private Collection $tblPlayers_MatchesPlayed;

    public function __construct()
    {
        $this->tblPlayers = new ArrayCollection();
        $this->tblPlayers_MatchesPlayed = new ArrayCollection();
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
     * @return Collection<int, TblPlayers>
     */
    public function getTblPlayers(): Collection
    {
        return $this->tblPlayers;
    }

    public function addTblPlayer(TblPlayers $tblPlayer): static
    {
        if (!$this->tblPlayers->contains($tblPlayer)) {
            $this->tblPlayers->add($tblPlayer);
            $tblPlayer->setTeam($this);
        }

        return $this;
    }

    public function removeTblPlayer(TblPlayers $tblPlayer): static
    {
        if ($this->tblPlayers->removeElement($tblPlayer)) {
            // set the owning side to null (unless already changed)
            if ($tblPlayer->getTeam() === $this) {
                $tblPlayer->setTeam(null);
            }
        }

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
     * @return Collection<int, TblPlayers>
     */
    public function getTblPlayersMatchesPlayed(): Collection
    {
        return $this->tblPlayers_MatchesPlayed;
    }

    public function addTblPlayersMatchesPlayed(TblPlayers $tblPlayersMatchesPlayed): static
    {
        if (!$this->tblPlayers_MatchesPlayed->contains($tblPlayersMatchesPlayed)) {
            $this->tblPlayers_MatchesPlayed->add($tblPlayersMatchesPlayed);
            $tblPlayersMatchesPlayed->setTeamMatchesPlayed($this);
        }

        return $this;
    }

    public function removeTblPlayersMatchesPlayed(TblPlayers $tblPlayersMatchesPlayed): static
    {
        if ($this->tblPlayers_MatchesPlayed->removeElement($tblPlayersMatchesPlayed)) {
            // set the owning side to null (unless already changed)
            if ($tblPlayersMatchesPlayed->getTeamMatchesPlayed() === $this) {
                $tblPlayersMatchesPlayed->setTeamMatchesPlayed(null);
            }
        }

        return $this;
    }
}
