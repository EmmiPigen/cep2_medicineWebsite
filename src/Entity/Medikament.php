<?php

namespace App\Entity;

use App\Repository\MedikamentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Symfony\Component\Validator\Constraints as Assert;

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
  #[Assert\LessThanOrEqual(1000, message: 'Dosis skal være mindre end 1000')]
  private ?float $dosis = null;

  #[ORM\Column(length: 5)]
  #[Assert\Choice(['mg', 'g', 'ml'], message: 'Dosis skal være i mg, g eller ml')]
  private ?string $unit = null;

  #[ORM\Column]
  #[Assert\Choice([1, 2, 3], message: 'Prioritet skal være 1, 2 eller 3')]
  private ?int $priority = null;

  #[ORM\Column]
  private ?int $timeInterval = null;

  #[ORM\Column]
  #[Assert\Range(min: 0.5, max: 10, notInRangeMessage: 'Mængde skal være mellem 0.5 og 10')]
  private ?float $amount = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $timeTaken = null;

  #[ORM\Column(nullable: true)]
  private ?bool $takenStatus = null;

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

  public function isTakenStatus(): ?bool
  {
    return $this->takenStatus;
  }

  public function setTakenStatus(?bool $takenStatus): static
  {
    $this->takenStatus = $takenStatus;

    return $this;
  }

  public function getUnit(): ?string
  {
    return $this->unit;
  }

  public function setUnit(string $unit): static
  {
    $this->unit = $unit;

    return $this;
  }
}
