<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $messageText = null;

    #[ORM\Column]
    private ?int $createTime = null;

    #[ORM\Column]
    private ?int $sendTime = null;

    #[ORM\Column]
    private ?int $receiver = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessageText(): ?string
    {
        return $this->messageText;
    }

    public function setMessageText(string $messageText): static
    {
        $this->messageText = $messageText;

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

    public function getReceiver(): ?int
    {
        return $this->receiver;
    }

    public function setReceiver(int $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }
}
