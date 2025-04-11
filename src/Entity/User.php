<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'user_id')]
    public ?int $userId = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $FuldeNavn = null;

    #[ORM\Column(length: 255)]
    private ?string $cpr = null;

    /**
     * @var Collection<int, MedikamentLog>
     */
    #[ORM\OneToMany(targetEntity: MedikamentLog::class, mappedBy: 'userId')]
    private Collection $medikamentLogs;

    /**
     * @var Collection<int, MedikamentListe>
     */
    #[ORM\OneToMany(targetEntity: MedikamentListe::class, mappedBy: 'userId')]
    private Collection $medikamentListes;

    /**
     * @var Collection<int, Udstyr>
     */
    
    #[ORM\OneToMany(targetEntity: Udstyr::class, mappedBy: 'userId')]
    private Collection $udstyrs;

    public function __construct()
    {
        $this->medikamentLogs = new ArrayCollection();
        $this->medikamentListes = new ArrayCollection();
        $this->udstyrs = new ArrayCollection();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFuldeNavn(): ?string
    {
        return $this->FuldeNavn;
    }

    public function setFuldeNavn(string $FuldeNavn): static
    {
        $this->FuldeNavn = $FuldeNavn;

        return $this;
    }

    public function getCpr(): ?string
    {
        return $this->cpr;
    }

    public function setCpr(string $cpr): static
    {
        $this->cpr = $cpr;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @return Collection<int, MedikamentLog>
     */
    public function getMedikamentLogs(): Collection
    {
        return $this->medikamentLogs;
    }

    public function addMedikamentLog(MedikamentLog $medikamentLog): static
    {
        if (!$this->medikamentLogs->contains($medikamentLog)) {
            $this->medikamentLogs->add($medikamentLog);
            $medikamentLog->setUserId($this);
        }

        return $this;
    }

    public function removeMedikamentLog(MedikamentLog $medikamentLog): static
    {
        if ($this->medikamentLogs->removeElement($medikamentLog)) {
            // set the owning side to null (unless already changed)
            if ($medikamentLog->getUserId() === $this) {
                $medikamentLog->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MedikamentListe>
     */
    public function getMedikamentListes(): Collection
    {
        return $this->medikamentListes;
    }

    public function addMedikamentListe(MedikamentListe $medikamentListe): static
    {
        if (!$this->medikamentListes->contains($medikamentListe)) {
            $this->medikamentListes->add($medikamentListe);
            $medikamentListe->setUserId($this);
        }

        return $this;
    }

    public function removeMedikamentListe(MedikamentListe $medikamentListe): static
    {
        if ($this->medikamentListes->removeElement($medikamentListe)) {
            // set the owning side to null (unless already changed)
            if ($medikamentListe->getUserId() === $this) {
                $medikamentListe->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Udstyr>
     */
    public function getUdstyrs(): Collection
    {
        return $this->udstyrs;
    }

    public function addUdstyr(Udstyr $udstyr): static
    {
        if (!$this->udstyrs->contains($udstyr)) {
            $this->udstyrs->add($udstyr);
            $udstyr->setUserId($this);
        }

        return $this;
    }

    public function removeUdstyr(Udstyr $udstyr): static
    {
        if ($this->udstyrs->removeElement($udstyr)) {
            // set the owning side to null (unless already changed)
            if ($udstyr->getUserId() === $this) {
                $udstyr->setUserId(null);
            }
        }

        return $this;
    }
}
