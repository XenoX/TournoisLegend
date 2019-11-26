<?php

namespace TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TournamentBundle\Workflow\ParticipantWorkflow;

/**
 * Participant
 *
 * @ORM\Table(name="participant")
 * @ORM\Entity(repositoryClass="TournamentBundle\Repository\ParticipantRepository")
 */
class Participant
{
    const MINIMUM_TEAM_PARTICIPANT = 2;
    const CAN_NOTHING = 0;
    const CAN_REGISTRATION = 1;
    const CAN_CONFIRM = 2;
    const CAN_CHECK_IN = 3;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="tag", type="string", length=255, nullable=true)
     */
    private $tag;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @Assert\NotNull()
     *
     * @ORM\Column(name="register_at", type="datetime")
     */
    private $registerAt;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     *
     * @ORM\Column(name="confirmed_at", type="datetime", nullable=true)
     */
    private $confirmedAt;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     *
     * @ORM\Column(name="check_in_at", type="datetime", nullable=true)
     */
    private $checkInAt;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="state", type="string", length=255)
     */
    private $state;

    /**
     * @var \UserBundle\Entity\User
     *
     * @Assert\Valid()
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var \UserBundle\Entity\Team
     *
     * @Assert\Valid()
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Team")
     */
    private $team;

    /**
     * @var \TournamentBundle\Entity\Tournament
     *
     * @Assert\Valid()
     *
     * @ORM\ManyToOne(targetEntity="TournamentBundle\Entity\Tournament")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tournament;

    /**
     * Participant constructor.
     */
    public function __construct()
    {
        $this->registerAt = new \DateTime();
    }

    /**
     * @return bool
     */
    public function isRegistered()
    {
        return ParticipantWorkflow::STATE_REGISTERED === $this->state;
    }

    /**
     * @return bool
     */
    public function isConfirmed()
    {
        return ParticipantWorkflow::STATE_CONFIRMED === $this->state;
    }

    /**
     * @return bool
     */
    public function isCheckIn()
    {
        return ParticipantWorkflow::STATE_CHECKED_IN === $this->state;
    }

    /**
     * @return \UserBundle\Entity\Team|\UserBundle\Entity\User
     */
    public function getTeamOrUser()
    {
        return $this->team ?? $this->user;
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
     * Set tag
     *
     * @param string $tag
     *
     * @return Participant
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set registerAt
     *
     * @param \DateTime $registerAt
     *
     * @return Participant
     */
    public function setRegisterAt($registerAt)
    {
        $this->registerAt = $registerAt;

        return $this;
    }

    /**
     * Get registerAt
     *
     * @return \DateTime
     */
    public function getRegisterAt()
    {
        return $this->registerAt;
    }

    /**
     * Set confirmedAt
     *
     * @param \DateTime $confirmedAt
     *
     * @return Participant
     */
    public function setConfirmedAt($confirmedAt)
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }

    /**
     * Get confirmedAt
     *
     * @return \DateTime
     */
    public function getConfirmedAt()
    {
        return $this->confirmedAt;
    }

    /**
     * Set checkInAt
     *
     * @param \DateTime $checkInAt
     *
     * @return Participant
     */
    public function setCheckInAt($checkInAt)
    {
        $this->checkInAt = $checkInAt;

        return $this;
    }

    /**
     * Get checkInAt
     *
     * @return \DateTime
     */
    public function getCheckInAt()
    {
        return $this->checkInAt;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Participant
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return Participant
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
     * @return Participant
     */
    public function setTeam(\UserBundle\Entity\Team $team = null)
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

    /**
     * Set tournament
     *
     * @param Tournament $tournament
     *
     * @return Participant
     */
    public function setTournament(Tournament $tournament)
    {
        $this->tournament = $tournament;

        return $this;
    }

    /**
     * Get tournament
     *
     * @return \TournamentBundle\Entity\Tournament
     */
    public function getTournament()
    {
        return $this->tournament;
    }
}
