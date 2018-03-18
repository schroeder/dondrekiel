<?php

namespace DondrekielAppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Actionlog
 *
 * @ORM\Table(name="actionlog", indexes={@ORM\Index(name="fk_actionlog_team1_idx", columns={"team_id"}), @ORM\Index(name="fk_actionlog_station1_idx", columns={"station_id"})})
 * @ORM\Entity
 */
class Actionlog
{

    const LOGLEVEL_TEAM_INFO = 1;
    const LOGLEVEL_TEAM_WARN = 2;
    const LOGLEVEL_TEAM_CRIT = 3;
    const LOGLEVEL_STATION_INFO = 4;
    const LOGLEVEL_STATION_WARN = 5;
    const LOGLEVEL_STATION_CRIT = 6;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="log_level", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $logLevel;

    /**
     * @var string
     *
     * @ORM\Column(name="log_text", type="string", nullable=true)
     */
    private $logText;

    /**
     * @var int
     *
     * @ORM\Column(name="timestamp", type="integer", nullable=true)
     */
    private $timestamp;

    /**
     * @var \DondrekielAppBundle\Entity\Station
     *
     * @ORM\OneToOne(targetEntity="DondrekielAppBundle\Entity\Station")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="station_id", referencedColumnName="id", unique=true)
     * })
     */
    private $station;

    /**
     * @var \DondrekielAppBundle\Entity\Team
     *
     * @ORM\ManyToOne(targetEntity="DondrekielAppBundle\Entity\Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="team_id", referencedColumnName="id")
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
     * Set id
     *
     * @param integer $id
     *
     * @return Actionlog
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
    public function getLogLevel()
    {
        return $this->logLevel;
    }

    /**
     * Set log level
     *
     * @param integer $id
     *
     * @return Actionlog
     */
    public function setLogLevel($logLevel)
    {
        $this->logLevel = $logLevel;

        return $this;
    }

    /**
     * Set logText
     *
     * @param string $logText
     *
     * @return Actionlog
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
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return Actionlog
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set station
     *
     * @param \DondrekielAppBundle\Entity\Station $station
     *
     * @return Actionlog
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
     * @return Actionlog
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
