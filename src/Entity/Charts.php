<?php

namespace App\Entity;

use App\Repository\ChartsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ChartsRepository::class)]
#[ORM\Table(name:'tbl_charts')]
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

    #[ORM\Column(length: 255)]
    private ?string $datascale_min = null;

    #[ORM\Column(length: 255)]
    private ?string $datascale_max = null;

    #[ORM\OneToMany(mappedBy: 'chart', targetEntity: Tests::class, cascade: ['persist'])]
    private Collection $tests;

    public function __construct()
    {
        $this->tests = new ArrayCollection();
    }

    public function getTests(): Collection
    {
        return $this->tests;
    }

    public function addTest(Tests $test): self
    {
        if (!$this->tests->contains($test)) {
            $this->tests[] = $test;
            $test->setChart($this);
        }

        return $this;
    }

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

    public function getDatascaleMin(): ?string
    {
        return $this->datascale_min;
    }

    public function setDatascaleMin(string $datascale_min): static
    {
        $this->datascale_min = $datascale_min;

        return $this;
    }

    public function getDatascaleMax(): ?string
    {
        return $this->datascale_max;
    }

    public function setDatascaleMax(string $datascale_max): static
    {
        $this->datascale_max = $datascale_max;

        return $this;
    }
}
