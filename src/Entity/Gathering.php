<?php

namespace App\Entity;

use App\Repository\GatheringRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GatheringRepository::class)]
#[ORM\Table(name: 'tbl_gathering')]
class Gathering
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $gathering_date = null;

    #[ORM\ManyToOne(inversedBy: 'gatherings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $Category = null;


    #[ORM\OneToMany(mappedBy: 'Gathering', targetEntity: Attendance::class)]
    private Collection $attendances;

    #[ORM\ManyToOne(inversedBy: 'gatherings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $MadeBy = null;

    public function __construct()
    {
        $this->attendances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGatheringDate(): ?\DateTimeInterface
    {
        return $this->gathering_date;
    }

    public function setGatheringDate(\DateTimeInterface $gathering_date): static
    {
        $this->gathering_date = $gathering_date;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->Category;
    }

    public function setCategory(?Category $Category): static
    {
        $this->Category = $Category;

        return $this;
    }

    /**
     * @return Collection<int, Attendance>
     */
    public function getAttendances(): Collection
    {
        return $this->attendances;
    }

    public function addAttendance(Attendance $attendance): static
    {
        if (!$this->attendances->contains($attendance)) {
            $this->attendances->add($attendance);
            $attendance->setGathering($this);
        }

        return $this;
    }

    public function removeAttendance(Attendance $attendance): static
    {
        if ($this->attendances->removeElement($attendance)) {
            // set the owning side to null (unless already changed)
            if ($attendance->getGathering() === $this) {
                $attendance->setGathering(null);
            }
        }

        return $this;
    }

    public function getMadeBy(): ?User
    {
        return $this->MadeBy;
    }

    public function setMadeBy(?User $MadeBy): static
    {
        $this->MadeBy = $MadeBy;

        return $this;
    }
}
