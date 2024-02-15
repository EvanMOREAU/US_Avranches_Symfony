<?php

namespace App\Entity;

use App\Repository\AttendanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttendanceRepository::class)]
#[ORM\Table(name: 'tbl_attendance')]
class Attendance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'attendances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;

    #[ORM\ManyToOne(inversedBy: 'attendances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Gathering $Gathering = null;

    #[ORM\Column]
    private ?bool $is_present = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reason = null;

    public function __toString(): string
    {
        return "attendance.id = '" . $this->getId()."' user.name = '" . $this->getUser()->getUsername()."'";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getGathering(): ?Gathering
    {
        return $this->Gathering;
    }

    public function setGathering(?Gathering $Gathering): static
    {
        $this->Gathering = $Gathering;

        return $this;
    }

    public function isIsPresent(): ?bool
    {
        return $this->is_present;
    }

    public function setIsPresent(bool $is_present): static
    {
        $this->is_present = $is_present;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }
}
