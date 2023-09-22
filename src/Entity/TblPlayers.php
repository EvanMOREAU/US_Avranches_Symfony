<?php

namespace App\Entity;

use App\Repository\TblPlayersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TblPlayersRepository::class)]
class TblPlayers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column]
    private ?int $birthyear = null;

    #[ORM\ManyToOne(inversedBy: 'tblPlayers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TblTeams $team = null;

    #[ORM\ManyToOne(inversedBy: 'tblPlayers_MatchesPlayed')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TblTeams $team_matches_played = null;

    #[ORM\Column]
    private ?int $player_matches_played = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getBirthyear(): ?int
    {
        return $this->birthyear;
    }

    public function setBirthyear(int $birthyear): static
    {
        $this->birthyear = $birthyear;

        return $this;
    }

    public function getTeam(): ?TblTeams
    {
        return $this->team;
    }

    public function setTeam(?TblTeams $team): static
    {
        $this->team = $team;

        return $this;
    }

    public function getTeamMatchesPlayed(): ?TblTeams
    {
        return $this->team_matches_played;
    }

    public function setTeamMatchesPlayed(?TblTeams $team_matches_played): static
    {
        $this->team_matches_played = $team_matches_played;

        return $this;
    }

    public function getPlayerMatchesPlayed(): ?int
    {
        return $this->player_matches_played;
    }

    public function setPlayerMatchesPlayed(int $player_matches_played): static
    {
        $this->player_matches_played = $player_matches_played;

        return $this;
    }
}
