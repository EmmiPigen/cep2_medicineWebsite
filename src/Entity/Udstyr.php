<?php

namespace App\Entity;

use App\Enum\Lokale;
use App\Repository\UdstyrRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UdstyrRepository::class)]
class Udstyr
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'udstyrs')]
    #[ORM\JoinColumn(name: 'userId', referencedColumnName: 'userId', nullable: false)]
    private ?User $userId = null;

    #[ORM\Column(enumType: Lokale::class)]
    private ?Lokale $lokale = null;

    #[ORM\Column(length: 255)]
    private ?string $enhed = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?int $power = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLokale(): ?Lokale
    {
        return $this->lokale;
    }

    public function setLokale(Lokale $lokale): static
    {
        $this->lokale = $lokale;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPower(): ?int
    {
        return $this->power;
    }

    public function setPower(int $power): static
    {
        $this->power = $power;

        return $this;
    }
}
