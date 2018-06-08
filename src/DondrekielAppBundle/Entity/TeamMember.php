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
 * @ORM\Table(name="team_member")
 * @ORM\Entity(repositoryClass="DondrekielAppBundle\Repository\TeamMemberRepository")
 */
class TeamMember
{
    const STATUS_NOTPLAYING = 0;
    const STATUS_PLAYER = 1;
    const STATUS_ATTENDANT = 2;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @ORM\Column(name="first_name", type="string")
     */
    protected $first_name;

    /**
     * @ORM\Column(name="email", type="string")
     */
    protected $email;

    /**
     * @ORM\Column(name="phone", type="string")
     */
    protected $phone;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    public function __construct()
    {
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
     * Set status
     *
     * @param int $status
     *
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return Team
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return Team
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return Team
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getEmail()
    {
        return $this->email;
    }

}
