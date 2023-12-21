<?php

namespace App\Entity;

use App\Entity\Tests;
use App\Entity\Height;
use App\Entity\Weight;
use App\Entity\Gathering;
use App\Entity\Attendance;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
#[UniqueEntity(fields: ['email'], message: 'Un compte possède déjà cette adresse mail.')]
#[ORM\Table(name:'tbl_user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_naissance = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $postePrincipal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $posteSecondaire = null;

    #[ORM\Column(nullable: true)]
    private ?float $posteCoordX = null;

    #[ORM\Column(nullable: true)]
    private ?float $posteCordY = null;

  #[ORM\OneToMany(mappedBy: 'User', targetEntity: Attendance::class)]
    private Collection $attendances;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Category $Category = null;

    #[ORM\OneToMany(mappedBy: 'MadeBy', targetEntity: Gathering::class)]
    private Collection $gatherings;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Tests::class)]
    private Collection $tests;

    public function __construct()
    {
        $this->attendances = new ArrayCollection();
        $this->gatherings = new ArrayCollection();
        $this->tests = new ArrayCollection();
        $this->weights = new ArrayCollection();
        $this->heights = new ArrayCollection();

    }


    #[ORM\Column(nullable: true)]
    private ?float $weight = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profile_image = null;

    

    /**
    * @Assert\NotBlank(groups={"registration", "resetPassword"})
    * @Assert\Length(
    *     min=6,
    *     minMessage="Votre mot de passe doit comporter au moins {{ limit }} caractères",
    *     groups={"registration", "resetPassword"}
    * )
    */
    private $plainPassword;

    #[ORM\Column]
    private ?bool $isCodeValidated = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Weight::class)]
    private Collection $weights;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Height::class)]
    private Collection $heights;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): static
    {
        $this->date_naissance = $date_naissance;

        return $this;
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

    public function getCategory(){
        $this_year = new \DateTime('first day of January next year');
        $diff = $this_year->diff($this->date_naissance);
        return 'U'.$diff->y + 1;
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
            $attendance->setUser($this);
        }

        return $this;
    }

    public function removeAttendance(Attendance $attendance): static
    {
        if ($this->attendances->removeElement($attendance)) {
            // set the owning side to null (unless already changed)
            if ($attendance->getUser() === $this) {
                $attendance->setUser(null);
            }
        }

        return $this;
    }

    public function setCategory(?Category $Category): static
    {
        $this->Category = $Category;

        return $this;
    }

    /**
     * @return Collection<int, Gathering>
     */
    public function getGatherings(): Collection
    {
        return $this->gatherings;
    }

    public function addGathering(Gathering $gathering): static
    {
        if (!$this->gatherings->contains($gathering)) {
            $this->gatherings->add($gathering);
            $gathering->setMadeBy($this);
        }

        return $this;
    }

    public function removeGathering(Gathering $gathering): static
    {
        if ($this->gatherings->removeElement($gathering)) {
            // set the owning side to null (unless already changed)
            if ($gathering->getMadeBy() === $this) {
                $gathering->setMadeBy(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Tests>
     */
    public function getTests(): Collection
    {
        return $this->tests;
    }

    public function addTest(Tests $test): static
    {
        if (!$this->tests->contains($test)) {
            $this->tests->add($test);
            $test->setUser($this);
        }
    }
    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }


    public function removeTest(Tests $test): static
    {
        if ($this->tests->removeElement($test)) {
            // set the owning side to null (unless already changed)
            if ($test->getUser() === $this) {
                $test->setUser(null);
            }
        }
    }
    public function getProfileImage(): ?string
    {
        return $this->profile_image;
    }

    public function setProfileImage(?string $profile_image): static
    {
        $this->profile_image = $profile_image;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password): self
    {
        $this->plainPassword = $password;

        return $this;
    }

    public function isIsCodeValidated(): ?bool
    {
        return $this->isCodeValidated;
    }

    public function setIsCodeValidated(bool $isCodeValidated): static
    {
        $this->isCodeValidated = $isCodeValidated;


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

    public function getPosteCoordX(): ?float
    {
        return $this->posteCoordX;
    }

    public function setPosteCoordX(?float $posteCoordX): static
    {
        $this->posteCoordX = $posteCoordX;

        return $this;
    }

    public function getPosteCordY(): ?float
    {
        return $this->posteCordY;
    }

    public function setPosteCordY(?float $posteCordY): static
    {
        $this->posteCordY = $posteCordY;

        return $this;
    }

    /**
     * @return Collection<int, Weight>
     */
    public function getWeights(): Collection
    {
        return $this->weights;
    }

    public function addWeight(Weight $weight): static
    {
        if (!$this->weights->contains($weight)) {
            $this->weights->add($weight);
            $weight->setUserId($this);
        }

        return $this;
    }

    public function removeWeight(Weight $weight): static
    {
        if ($this->weights->removeElement($weight)) {
            // set the owning side to null (unless already changed)
            if ($weight->getUserId() === $this) {
                $weight->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Height>
     */
    public function getHeights(): Collection
    {
        return $this->heights;
    }

    public function addHeight(Height $height): static
    {
        if (!$this->heights->contains($height)) {
            $this->heights->add($height);
            $height->setUserId($this);
        }

        return $this;
    }

    public function removeHeight(Height $height): static
    {
        if ($this->heights->removeElement($height)) {
            // set the owning side to null (unless already changed)
            if ($height->getUserId() === $this) {
                $height->setUserId(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
