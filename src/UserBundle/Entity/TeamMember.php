<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TeamMember
 *
 * @ORM\Table(name="team_member")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\TeamMemberRepository")
 */
class TeamMember
{
    const ROLE_LEADER = 'role.leader';
    const ROLE_MANAGER = 'role.manager';
    const ROLE_COACH = 'role.coach';
    const ROLE_STREAMER = 'role.streamer';
    const ROLE_PLAYER = 'role.player';
    const ROLE_TROLL = 'role.troll';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @Assert\NotNull()
     *
     * @ORM\Column(name="join_at", type="datetime")
     */
    private $joinAt;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="role", type="string", length=255)
     */
    private $role;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="requested", type="boolean")
     */
    private $requested;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Team")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team;

    /**
     * TeamMember constructor.
     */
    public function __construct()
    {
        $this->joinAt = new \DateTime();
        $this->role = self::ROLE_PLAYER;
        $this->requested = false;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set joinAt
     *
     * @param \DateTime $joinAt
     *
     * @return TeamMember
     */
    public function setJoinAt($joinAt)
    {
        $this->joinAt = $joinAt;

        return $this;
    }

    /**
     * Get joinAt
     *
     * @return \DateTime
     */
    public function getJoinAt()
    {
        return $this->joinAt;
    }

    /**
     * Set token
     *
     * @param string $role
     *
     * @return TeamMember
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set requested
     *
     * @param boolean $requested
     *
     * @return TeamMember
     */
    public function setRequested($requested)
    {
        $this->requested = $requested;

        return $this;
    }

    /**
     * Get requested
     *
     * @return boolean
     */
    public function getRequested()
    {
        return $this->requested;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return TeamMember
     */
    public function setUser(\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set team
     *
     * @param \UserBundle\Entity\Team $team
     *
     * @return TeamMember
     */
    public function setTeam(\UserBundle\Entity\Team $team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \UserBundle\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }
}
