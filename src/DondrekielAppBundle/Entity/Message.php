<?php

namespace DondrekielAppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Location
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="DondrekielAppBundle\Repository\MessageRepository")
 *
 */
class Message
{

    const TYPE_TEAM = 1;
    const TYPE_STATION = 2;
    const TYPE_ALL = 3;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="message_text", type="text", nullable=true)
     */
    private $messageText;

    /**
     * @var integer
     *
     * @ORM\Column(name="create_time", type="integer", nullable=true)
     */
    private $createTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="send_time", type="integer", nullable=true)
     */
    private $sendTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="receiver", type="integer", nullable=true)
     */
    private $receiver;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set messageText
     *
     * @param string $messageText
     *
     * @return Message
     */
    public function setMessageText($messageText)
    {
        $this->messageText = $messageText;

        return $this;
    }

    /**
     * Get messageText
     *
     * @return string
     */
    public function getMessageText()
    {
        return $this->messageText;
    }

    /**
     * Set sendTime
     *
     * @param \DateTime $sendTime
     *
     * @return Message
     */
    public function setSendTime($sendTime)
    {
        $this->sendTime = $sendTime;

        return $this;
    }

    /**
     * Get sendTime
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Get sendTime
     *
     * @return \DateTime
     */
    public function getSendTime()
    {
        return $this->sendTime;
    }

    /**
     * Set sendTime
     *
     * @param \DateTime $sendTime
     *
     * @return Message
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Set readTime
     *
     * @param \DateTime $readTime
     *
     * @return Message
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Get readTime
     *
     * @return \DateTime
     */
    public function getReceiver()
    {
        return $this->receiver;
    }
}
