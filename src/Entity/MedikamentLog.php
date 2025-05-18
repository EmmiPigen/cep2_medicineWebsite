<?php

namespace App\Entity;

use App\Repository\MedikamentLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\Lokale;

#[ORM\Entity(repositoryClass: MedikamentLogRepository::class)]
class MedikamentLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $medikamentNavn = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $tagetTid = null;

    #[ORM\Column(length: 255)]
    private ?string $tagetStatus = null;

    #[ORM\Column(length: 255)]
    private ?string $tagetLokale;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'medikamentLogs')]
    #[ORM\JoinColumn(name: 'userId', referencedColumnName: 'user_id', nullable: false)]
    public ?User $userId = null;

    # til sms service
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $alarmSent = false;

    
    public function isAlarmSent(): bool
    {
        return $this->alarmSent;
    }

    public function setAlarmSent(bool $sent): static
    {
        $this->alarmSent = $sent;
        return $this;
    }


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

    public function getTagetTid(): ?\DateTimeInterface
    {
        return $this->tagetTid;
    }

    public function setTagetTid(?\DateTimeInterface $tagetTid): static
    {
        $this->tagetTid = $tagetTid;

        return $this;
    }

    public function getTagetStatus(): ?string
    {
        return $this->tagetStatus;
    }

    public function setTagetStatus(string $tagetStatus): static
    {
        $this->tagetStatus = $tagetStatus;

        return $this;
    }

    public function setTagetLokale(Lokale $lokale): void
    {
      $this->tagetLokale = $lokale->value;

    }
    public function getTagetLokale(): Lokale
    {
      return Lokale::from($this->tagetLokale);
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
