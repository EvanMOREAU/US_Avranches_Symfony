<?php

namespace App\Entity;

use App\Repository\ChartsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChartsRepository::class)]
class Charts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $data = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $source_data = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(string $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getSourceData(): ?string
    {
        return $this->source_data;
    }

    public function getSource_Data(): ?string
    {
        return $this->source_data;
    }

    public function setSourceData(string $source_data): static
    {
        $this->source_data = $source_data;

        return $this;
    }
}
