<?php

namespace App\Entity;

use App\Repository\PositionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PositionRepository::class)]
class Position
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?team $team = null;

    #[ORM\Column]
    private ?float $locationLng = null;

    #[ORM\Column]
    private ?float $locationLat = null;

    #[ORM\Column]
    private ?int $timestamp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam(): ?team
    {
        return $this->team;
    }

    public function setTeam(?team $team): static
    {
        $this->team = $team;

        return $this;
    }

    public function getLocationLng(): ?float
    {
        return $this->locationLng;
    }

    public function setLocationLng(float $locationLng): static
    {
        $this->locationLng = $locationLng;

        return $this;
    }

    public function getLocationLat(): ?float
    {
        return $this->locationLat;
    }

    public function setLocationLat(float $locationLat): static
    {
        $this->locationLat = $locationLat;

        return $this;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
