<?php

namespace App\Entity;

use App\Repository\MedikamentAlarmLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedikamentAlarmLogRepository::class)]
class MedikamentAlarmLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $medikamentNavn;

    #[ORM\Column(type: 'datetime', )]
    private \DateTimeInterface $dato;

    #[ORM\Column(length: 5)]
    private string $tidspunkt; // f.eks. "08:00"

    #[ORM\ManyToOne(inversedBy: 'medikamentAlarmLogs')]
    #[ORM\JoinColumn(name: 'userId', referencedColumnName: 'user_id', nullable: false)]
    private ?User $user = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedikamentNavn(): string
    {
        return $this->medikamentNavn;
    }

    public function setMedikamentNavn(string $medikamentNavn): static
    {
        $this->medikamentNavn = $medikamentNavn;
        return $this;
    }

    public function getDato(): \DateTimeInterface
    {
        return $this->dato;
    }

    public function setDato(\DateTimeInterface $dato): static
    {
        $this->dato = $dato;
        return $this;
    }

    public function getTidspunkt(): string
    {
        return $this->tidspunkt;
    }

    public function setTidspunkt(string $tidspunkt): static
    {
        $this->tidspunkt = $tidspunkt;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }
}
