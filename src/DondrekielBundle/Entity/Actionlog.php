<?php

namespace DondrekielBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Actionlog
 *
 * @ORM\Table(name="actionlog", indexes={@ORM\Index(name="fk_actionlog_team1_idx", columns={"team_id"}), @ORM\Index(name="fk_actionlog_game1_idx", columns={"game_id"})})
 * @ORM\Entity
 */
class Actionlog
{

    const LOGLEVEL_TEAM_INFO = 1;
    const LOGLEVEL_TEAM_WARN = 2;
    const LOGLEVEL_TEAM_CRIT = 3;
    const LOGLEVEL_GAME_INFO = 4;
    const LOGLEVEL_GAME_WARN = 5;
    const LOGLEVEL_GAME_CRIT = 6;

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
     * @var \DondrekielBundle\Entity\Game
     *
     * @ORM\OneToOne(targetEntity="DondrekielBundle\Entity\Game")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="game_id", referencedColumnName="id", unique=true)
     * })
     */
    private $game;

    /**
     * @var \DondrekielBundle\Entity\Team
     *
     * @ORM\ManyToOne(targetEntity="DondrekielBundle\Entity\Team")
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
     * Set game
     *
     * @param \DondrekielBundle\Entity\Game $game
     *
     * @return Actionlog
     */
    public function setGame(\DondrekielBundle\Entity\Game $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return \DondrekielBundle\Entity\Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set team
     *
     * @param \DondrekielBundle\Entity\Team $team
     *
     * @return Actionlog
     */
    public function setTeam(\DondrekielBundle\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \DondrekielBundle\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }
}
