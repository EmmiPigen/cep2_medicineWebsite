<?php

namespace App\Entity;

use App\Repository\MedikamentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: MedikamentRepository::class)]
#[Broadcast]
class Medikament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $dosis = null;

    #[ORM\Column]
    private ?int $priority = null;

    #[ORM\Column]
    private ?int $timeInterval = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $timeTaken = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDosis(): ?float
    {
        return $this->dosis;
    }

    public function setDosis(float $dosis): static
    {
        $this->dosis = $dosis;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getTimmInterval(): ?int
    {
        return $this->timeInterval;
    }

    public function setTimeInterval(int $timmInterval): static
    {
        $this->timeInterval = $timmInterval;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getTimeTaken(): ?\DateTimeInterface
    {
        return $this->timeTaken;
    }

    public function setTimeTaken(\DateTimeInterface $timeTaken): static
    {
        $this->timeTaken = $timeTaken;

        return $this;
    }
}
