<?php

namespace App\Entity;

use App\Repository\MedikamentListeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedikamentListeRepository::class)]
class MedikamentListe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    private ?string $medikamentNavn = null;


    #[ORM\Column]
    private ?int $timeInterval = null;

    #[ORM\Column]
    private ?int $dosis = null;

    #[ORM\Column(length: 4)]
    private ?string $enhed = null;

    #[ORM\ManyToOne(inversedBy: 'medikamentListes')]
    #[ORM\JoinColumn(name: 'userId', referencedColumnName: 'user_id', nullable: false)]
    private ?User $userId = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $tidspunktTages = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getMedikamentNavn(): ?string
    {
        return $this->medikamentNavn;
    }

    public function setMedikamentNavn(string $medikamentNavn): static
    {
        $this->medikamentNavn = $medikamentNavn;

        return $this;
    }


    public function getTimeInterval(): ?int
    {
        return $this->timeInterval;
    }

    public function setTimeInterval(int $timeInterval): static
    {
        $this->timeInterval = $timeInterval;

        return $this;
    }

    public function getDosis(): ?int
    {
        return $this->dosis;
    }

    public function setDosis(int $dosis): static
    {
        $this->dosis = $dosis;

        return $this;
    }

    public function getEnhed(): ?string
    {
        return $this->enhed;
    }

    public function setEnhed(string $enhed): static
    {
        $this->enhed = $enhed;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getTidspunktTages(): ?\DateTimeInterface
    {
        return $this->tidspunktTages;
    }

    public function setTidspunktTages(\DateTimeInterface $tidspunktTages): static
    {
        $this->tidspunktTages = $tidspunktTages;

        return $this;
    }
}
