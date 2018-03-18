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
     * @var string
     *
     * @ORM\Column(name="send_time", type="integer", nullable=true)
     */
    private $sendTime;

    /**
     * @var string
     *
     * @ORM\Column(name="read_time", type="integer", nullable=true)
     */
    private $readTime;


    /**
     * @var \DondrekielAppBundle\Entity\Station
     *
     * @ORM\ManyToOne(targetEntity="DondrekielAppBundle\Entity\Station")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="station", referencedColumnName="id")
     * })
     */
    private $station;

    /**
     * @var \DondrekielAppBundle\Entity\Team
     *
     * @ORM\ManyToOne(targetEntity="DondrekielAppBundle\Entity\Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="team", referencedColumnName="id")
     * })
     */
    private $team;


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
    public function getSendTime()
    {
        return $this->sendTime;
    }

    /**
     * Set readTime
     *
     * @param \DateTime $readTime
     *
     * @return Message
     */
    public function setReadTime($readTime)
    {
        $this->readTime = $readTime;

        return $this;
    }

    /**
     * Get readTime
     *
     * @return \DateTime
     */
    public function getReadTime()
    {
        return $this->readTime;
    }

    /**
     * Set station
     *
     * @param \DondrekielAppBundle\Entity\Station $station
     *
     * @return Message
     */
    public function setStation(\DondrekielAppBundle\Entity\Station $station = null)
    {
        $this->station = $station;

        return $this;
    }

    /**
     * Get station
     *
     * @return \DondrekielAppBundle\Entity\Station
     */
    public function getStation()
    {
        return $this->station;
    }

    /**
     * Set team
     *
     * @param \DondrekielAppBundle\Entity\Team $team
     *
     * @return Message
     */
    public function setTeam(\DondrekielAppBundle\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \DondrekielAppBundle\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }
}
