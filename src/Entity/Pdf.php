<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PdfRepository;

#[ORM\Entity(repositoryClass: PdfRepository::class)]
class Pdf
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    private ?int $stats = null;


    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;

    function getName(): ?string
    {
        return $this->name;
    }

    function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    function getContent(): ?string
    {
        return $this->content;
    }

    function setContent(?string $content)
    {
        $this->content = $content;

        return $this;
    }

    function getStats(): ?int
    {
        return $this->stats;
    }

    function SetStats(?int $stats)
    {
        $this->stats = $stats;

        return $this;
    }

    }
}
