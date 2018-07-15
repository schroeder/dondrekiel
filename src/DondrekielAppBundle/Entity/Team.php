<?php

namespace DondrekielAppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use DondrekielAppBundle\Entity\Actionlog;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use FOS\UserBundle\Model\User as BaseUser;
use Oh\GoogleMapFormTypeBundle\Validator\Constraints as OhAssert;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="DondrekielAppBundle\Repository\TeamRepository")
 */
class Team extends BaseUser
{
    const STATUS_UNUSED = 0;
    const STATUS_IN_REGISTRATION = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_BLOCKED = 3;
    const STATUS_ADMIN = 4;
    const STATUS_STATION = 5;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string")
     */
    private $comment;

    /**
     * @var Collection
     *
     * @OneToMany(targetEntity="Actionlog", mappedBy="team", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    private $logEntries;

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

    public function __construct()
    {
        parent::__construct();
        $this->status = 0;

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
     * Get memberOfTeam
     *
     * @return ArrayCollection<DondrekielAppBundle\Entity\Actionlog>
     */
    public function getLogEntries()
    {
        return $this->logEntries;
    }


    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return Team
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get name
     *
     * @return integer
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param int $name
     *
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return integer
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set name
     *
     * @param int $comment
     *
     * @return Team
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
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

}
