<?php

namespace DondrekielAppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use DondrekielAppBundle\Entity\Member;
use DondrekielAppBundle\Entity\Level;
use DondrekielAppBundle\Repository\TeamLevelRepository;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use FOS\UserBundle\Model\User as BaseUser;
use Oh\GoogleMapFormTypeBundle\Validator\Constraints as OhAssert;

/**
 * Team
 *
 * @ORM\Table(name="position")
 * @ORM\Entity(repositoryClass="DondrekielAppBundle\Repository\PositionRepository")
 */
class Position
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * @var float
     *
     * @ORM\Column(name="location_lng", type="float", precision=12, scale=2, nullable=true)
     */
    private $locationLng;

    /**
     * @var float
     *
     * @ORM\Column(name="location_lat", type="float", precision=12, scale=2, nullable=true)
     */
    private $locationLat;

    /**
     * @var int
     *
     * @ORM\Column(name="timestamp", type="integer", nullable=true)
     */
    private $timestamp;

    public function __construct()
    {

    }

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
     * Set team
     *
     * @param \DondrekielAppBundle\Entity\Team $team
     *
     * @return Position
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

    /**
     * Set status
     *
     * @param float $locationLat
     *
     * @return Position
     */
    public function setLocationLat($locationLat)
    {
        $this->locationLat = $locationLat;

        return $this;
    }

    /**
     * Get locationLat
     *
     * @return float
     */
    public function getLocationLat()
    {
        return $this->locationLat;
    }

    /**
     * Set locationLng
     *
     * @param float $locationLng
     *
     * @return Position
     */
    public function setLocationLng($locationLng)
    {
        $this->locationLng = $locationLng;

        return $this;
    }

    /**
     * Get locationLng
     *
     * @return float
     */
    public function getLocationLng()
    {
        return $this->locationLng;
    }

    public function setLocation($latlng)
    {
        $this->setLocationLat($latlng['lat']);
        $this->setLocationLng($latlng['lng']);
        return $this;
    }

    /**
     * @OhAssert\LatLng()
     */
    public function getLocation()
    {
        return array('lat' => $this->getLocationLat(), 'lng' => $this->getLocationLng());
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return Position
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
        return new \DateTime($this->timestamp);
    }
}
