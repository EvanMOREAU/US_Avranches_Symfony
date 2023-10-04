<?php

namespace App\Entity;

use App\Repository\TestEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestEntityRepository::class)]
class TestEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $vma = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $cooper = null;

    #[ORM\Column(nullable: true)]
    private ?int $jongle_gauche = null;

    #[ORM\Column(nullable: true)]
    private ?int $jongle_droit = null;

    #[ORM\Column(nullable: true)]
    private ?int $jongle_tete = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVma(): ?int
    {
        return $this->vma;
    }

    public function setVma(?int $vma): static
    {
        $this->vma = $vma;

        return $this;
    }

    public function getCooper(): ?\DateTimeInterface
    {
        return $this->cooper;
    }

    public function setCooper(?\DateTimeInterface $cooper): static
    {
        $this->cooper = $cooper;

        return $this;
    }

    public function getJongleGauche(): ?int
    {
        return $this->jongle_gauche;
    }

    public function setJongleGauche(?int $jongle_gauche): static
    {
        $this->jongle_gauche = $jongle_gauche;

        return $this;
    }

    public function getJongleDroit(): ?int
    {
        return $this->jongle_droit;
    }

    public function setJongleDroit(?int $jongle_droit): static
    {
        $this->jongle_droit = $jongle_droit;

        return $this;
    }

    public function getJongleTete(): ?int
    {
        return $this->jongle_tete;
    }

    public function setJongleTete(?int $jongle_tete): static
    {
        $this->jongle_tete = $jongle_tete;

        return $this;
    }
}
