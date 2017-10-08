<?php

namespace DondrekielBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Member
 *
 * @ORM\Table(name="member")
 * @ORM\Entity(repositoryClass="DondrekielBundle\Repository\MemberRepository")
 */
class Member
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
     * @ORM\Column(name="passcode", type="string", length=45, nullable=true)
     */
    private $passcode;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * Der Stamm
     *
     * @ORM\Column(name="town", type="string", length=255, nullable=true)
     */
    private $group;

    /**
     * @var string
     *
     * Die Stufe
     *
     * @ORM\Column(name="grade", type="string", length=45, nullable=true)
     */
    private $grade;

    /**
     * @var string
     *
     * Das Dorf
     *
     * @ORM\Column(name="village", type="string", length=255, nullable=true)
     */
    private $village;

    /**
     * @var \DondrekielBundle\Entity\Team
     *
     * @ORM\ManyToOne(targetEntity="DondrekielBundle\Entity\Team", fetch="EAGER", inversedBy="teamMembers")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
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
     * Set passcode
     *
     * @param string $passcode
     *
     * @return Member
     */
    public function setPasscode($passcode)
    {
        $this->passcode = $passcode;
        return $this;
    }

    /**
     * Get passcode
     *
     * @return string
     */
    public function getPasscode()
    {
        return $this->passcode;
    }

    /**
     * Get full name
     *
     * @param string $name
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->firstName . " " . $this->name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Member
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set first name
     *
     * @param string $name
     *
     * @return Member
     */
    public function setFirstName($name)
    {
        $this->firstName = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set first name
     *
     * @param string $name
     *
     * @return Member
     */
    public function setGroup($name)
    {
        $this->group = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set grade
     *
     * @param string $grade
     *
     * @return Member
     */
    public function setGrade($stufe)
    {
        $this->grade = $stufe;

        return $this;
    }

    /**
     * Get grade
     *
     * @return string
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Set grade
     *
     * @param string $grade
     *
     * @return Member
     */
    public function setVillage($village)
    {
        $this->village = $village;

        return $this;
    }

    /**
     * Get grade
     *
     * @return string
     */
    public function getVillage()
    {
        return $this->village;
    }

    /**
     * Set team
     *
     * @param \DondrekielBundle\Entity\Team $team
     *
     * @return Member
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
