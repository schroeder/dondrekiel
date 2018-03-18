<?php

namespace DondrekielAppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use DondrekielAppBundle\Entity\Level;
use DondrekielAppBundle\Repository\TeamLevelRepository;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use FOS\UserBundle\Model\User as BaseUser;

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
     * @var Collection
     *
     * @OneToMany(targetEntity="Actionlog", mappedBy="team", cascade={"persist", "remove", "merge"}, orphanRemoval=true, mappedBy="logEntries")
     */
    private $logEntries;

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


    /**
     * Get countPersons
     *
     * @return integer
     */
    public function getCountMembers()
    {
        return $this->getTeamMembers() ? $this->getTeamMembers()->count() : 0;
    }

    public function getRoles()
    {
        return array('ROLE_TEAM');
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
}
