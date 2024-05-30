<?php

namespace App\Entity;

use App\Repository\ActionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActionRepository::class)]
class Action
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $action = null;

    #[ORM\Column(length: 255)]
    private ?string $logText = null;

    #[ORM\Column]
    private ?int $createTime = null;

    #[ORM\Column]
    private ?int $sendTime = null;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    private ?Station $station = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAction(): ?int
    {
        return $this->action;
    }

    public function setAction(int $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getLogText(): ?string
    {
        return $this->logText;
    }

    public function setLogText(string $logText): static
    {
        $this->logText = $logText;

        return $this;
    }

    public function getCreateTime(): ?int
    {
        return $this->createTime;
    }

    public function setCreateTime(int $createTime): static
    {
        $this->createTime = $createTime;

        return $this;
    }

    public function getSendTime(): ?int
    {
        return $this->sendTime;
    }

    public function setSendTime(int $sendTime): static
    {
        $this->sendTime = $sendTime;

        return $this;
    }

    public function getStation(): ?Station
    {
        return $this->station;
    }

    public function setStation(?Station $station): static
    {
        $this->station = $station;

        return $this;
    }
}
