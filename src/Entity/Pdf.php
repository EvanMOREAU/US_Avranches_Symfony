<?php

namespace App\Entity;

use App\Repository\PdfRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: PdfRepository::class)]
class Pdf
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    private ?int $stats = null;




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
