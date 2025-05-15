<?php
namespace App\Entity;

use App\Entity\MedikamentAlarmLog;
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

    /**
     * @var Collection<int, MedikamentAlarmLog>
     */
    #[ORM\OneToMany(targetEntity: MedikamentAlarmLog::class, mappedBy: 'userId')]
    private Collection $medikamentAlarmLogs;

    // Til at indlæse information om kontakperson
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Fulde navn må ikke være tomt')]
    private ?string $omsorgspersonNavn = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Telefonnummer må ikke være tomt')]
    private ?string $omsorgspersonTelefon = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Email må ikke være tomt')]
    #[Assert\Email(message: 'Ugyldig emailadresse')]
    private ?string $omsorgspersonEmail = null;

    // Til profilbillede
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilBillede = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telefonNummer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $byNavn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $postnummer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $land = null;

    public function __construct()
    {
        $this->medikamentLogs   = new ArrayCollection();
        $this->medikamentListes = new ArrayCollection();
        $this->udstyrs          = new ArrayCollection();
        $this->medikamentAlarmLogs = new ArrayCollection();
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

    public function getOmsorgspersonNavn(): ?string
    {
        return $this->omsorgspersonNavn;
    }

    public function setOmsorgspersonNavn(?string $omsorgspersonNavn): static
    {
        $this->omsorgspersonNavn = $omsorgspersonNavn;
        return $this;
    }

    public function getOmsorgspersonTelefon(): ?string
    {
        return $this->omsorgspersonTelefon;
    }

    public function setOmsorgspersonTelefon(?string $omsorgspersonTelefon): static
    {
        if ($omsorgspersonTelefon !== null) {
            // Fjern mellemrum
            $omsorgspersonTelefon = str_replace(' ', '', $omsorgspersonTelefon);

            // Hvis nummeret ikke allerede starter med +, tilføj +45
            if (! str_starts_with($omsorgspersonTelefon, '+')) {
                $omsorgspersonTelefon = '+45' . $omsorgspersonTelefon;
            }
        }

        $this->omsorgspersonTelefon = $omsorgspersonTelefon;
        return $this;
    }

    public function getOmsorgspersonEmail(): ?string
    {
        return $this->omsorgspersonEmail;
    }

    public function setOmsorgspersonEmail(?string $omsorgspersonEmail): static
    {
        $this->omsorgspersonEmail = $omsorgspersonEmail;
        return $this;
    }

    // Oploader profilbillede til user-databasen
    public function getProfilBillede(): ?string
    {
        return $this->profilBillede;
    }

    public function setProfilBillede(?string $profilBillede): self
    {
        $this->profilBillede = $profilBillede;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
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
        if (! $this->medikamentLogs->contains($medikamentLog)) {
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
        if (! $this->medikamentListes->contains($medikamentListe)) {
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
        if (! $this->udstyrs->contains($udstyr)) {
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

    

    public function getTelefonNummer(): ?string
    {
        return $this->telefonNummer;
    }

    public function setTelefonNummer(?string $telefonNummer): static
    {
        $this->telefonNummer = $telefonNummer;

        return $this;
    }

    public function getAddresse(): ?string
    {
        return $this->addresse;
    }

    public function setAddresse(?string $addresse): static
    {
        $this->addresse = $addresse;

        return $this;
    }

    public function getByNavn(): ?string
    {
        return $this->byNavn;
    }

    public function setByNavn(?string $byNavn): static
    {
        $this->byNavn = $byNavn;

        return $this;
    }

    public function getPostnummer(): ?string
    {
        return $this->postnummer;
    }

    public function setPostnummer(?string $postnummer): static
    {
        $this->postnummer = $postnummer;

        return $this;
    }

    public function getLand(): ?string
    {
        return $this->land;
    }

    public function setLand(?string $land): static
    {
        $this->land = $land;

        return $this;
    }

    /**
     * @return Collection<int, MedikamentAlarmLog>
     */
    public function getMedikamentAlarmLogs(): Collection
    {
        return $this->medikamentAlarmLogs;
    }

    public function addMedikamentAlarmLog(MedikamentAlarmLog $medikamentAlarmLog): static
    {
        if (!$this->medikamentAlarmLogs->contains($medikamentAlarmLog)) {
            $this->medikamentAlarmLogs->add($medikamentAlarmLog);
            $medikamentAlarmLog->setUserId($this);
        }

        return $this;
    }

    public function removeMedikamentAlarmLog(MedikamentAlarmLog $medikamentAlarmLog): static
    {
        if ($this->medikamentAlarmLogs->removeElement($medikamentAlarmLog)) {
            // set the owning side to null (unless already changed)
            if ($medikamentAlarmLog->getUserId() === $this) {
                $medikamentAlarmLog->setUserId(null);
            }
        }

        return $this;
    }
}
