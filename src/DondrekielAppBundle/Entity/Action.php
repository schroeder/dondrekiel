<?php

namespace DondrekielAppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\GeneratedValue;

/**
 * Action
 *
 * @ORM\Table(name="action")
 * @ORM\Entity(repositoryClass="DondrekielAppBundle\Repository\ActionRepository")
 */
class Action
{

    const ACTION_STATION_ACTIVE = 1;
    const ACTION_STATION_INACTIVE = 2;

    /**
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="action", type="integer")
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(name="log_text", type="string", nullable=true)
     */
    private $logText;

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
     * @var \DondrekielAppBundle\Entity\Station
     *
     * @ORM\ManyToOne(targetEntity="DondrekielAppBundle\Entity\Station")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="station_id", referencedColumnName="id", unique=false)
     * })
     */
    private $station;


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
     * Set id
     *
     * @param integer $id
     *
     * @return Action
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get log level
     *
     * @return integer
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set log level
     *
     * @param integer $id
     *
     * @return Action
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Set logText
     *
     * @param string $logText
     *
     * @return Action
     */
    public function setLogText($logText)
    {
        $this->logText = $logText;

        return $this;
    }

    /**
     * Get logText
     *
     * @return string
     */
    public function getLogText()
    {
        return $this->logText;
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
     * Set station
     *
     * @param \DondrekielAppBundle\Entity\Station $station
     *
     * @return Action
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
}
