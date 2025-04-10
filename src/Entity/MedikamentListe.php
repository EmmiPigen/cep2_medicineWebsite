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

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $tidspunkterTages = [];

    #[ORM\Column]
    private ?int $timeInterval = null;

    #[ORM\Column]
    private ?int $dosis = null;

    #[ORM\Column(length: 4)]
    private ?string $enhed = null;

    #[ORM\ManyToOne(inversedBy: 'medikamentListes')]
    #[ORM\JoinColumn(name: 'userId', referencedColumnName: 'userId', nullable: false)]
    private ?User $userId = null;

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

    public function getTidspunkterTages(): array
    {
        return $this->tidspunkterTages;
    }

    public function setTidspunkterTages(array $tidspunkterTages): static
    {
        $this->tidspunkterTages = $tidspunkterTages;

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
}
