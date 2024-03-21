<?php

namespace App\Entity;

use App\Repository\ChartConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChartConfigurationRepository::class)]
class ChartConfiguration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $chartType = null;

    #[ORM\Column]
    private array $configData = [];

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChartType(): ?string
    {
        return $this->chartType;
    }

    public function setChartType(string $chartType): static
    {
        $this->chartType = $chartType;

        return $this;
    }

    public function getConfigData(): array
    {
        return $this->configData;
    }

    public function setConfigData(array $configData): static
    {
        $this->configData = $configData;

        return $this;
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
}
