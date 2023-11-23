<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
#[ORM\Table(name: 'tbl_player')]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\Column]
    private ?int $matches_played = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $postePrincipal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $posteSecondaire = null;

    #[ORM\Column(nullable: true)]
    private ?int $posteCoordX = null;

    #[ORM\Column(nullable: true)]
    private ?int $posteCordY = null;

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

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;

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

    public function getPostePrincipal(): ?string
    {
        return $this->postePrincipal;
    }

    public function setPostePrincipal(?string $postePrincipal): static
    {
        $this->postePrincipal = $postePrincipal;

        return $this;
    }

    public function getPosteSecondaire(): ?string
    {
        return $this->posteSecondaire;
    }

    public function setPosteSecondaire(?string $posteSecondaire): static
    {
        $this->posteSecondaire = $posteSecondaire;

        return $this;
    }

    public function getPosteCoordX(): ?int
    {
        return $this->posteCoordX;
    }

    public function setPosteCoordX(?int $posteCoordX): static
    {
        $this->posteCoordX = $posteCoordX;

        return $this;
    }

    public function getPosteCordY(): ?int
    {
        return $this->posteCordY;
    }

    public function setPosteCordY(?int $posteCordY): static
    {
        $this->posteCordY = $posteCordY;

        return $this;
    }
}

