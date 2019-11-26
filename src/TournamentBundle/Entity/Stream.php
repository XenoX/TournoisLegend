<?php

namespace TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TournamentBundle\Workflow\StreamWorkflow;
use UserBundle\Entity\User;

/**
 * Stream
 *
 * @ORM\Table(name="stream")
 * @ORM\Entity(repositoryClass="TournamentBundle\Repository\StreamRepository")
 */
class Stream
{
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
     * @Assert\NotNull()
     *
     * @ORM\Column(name="channel", type="string", length=255)
     */
    private $channel;

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
     * @var bool
     *
     * @Assert\Type("boolean")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="main", type="boolean")
     */
    private $main;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @Assert\NotNull()
     *
     * @ORM\Column(name="requested_at", type="datetime")
     */
    private $requestedAt;

    /**
     * @var Tournament
     *
     * @Assert\Valid()
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="TournamentBundle\Entity\Tournament")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tournament;

    /**
     * @var \UserBundle\Entity\User
     *
     * @Assert\Valid()
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn()
     */
    private $user;

    /**
     * @var Organizer
     *
     * @Assert\Valid()
     *
     * @ORM\ManyToOne(targetEntity="Organizer")
     * @ORM\JoinColumn()
     */
    private $organizer;

    /**
     * Stream constructor.
     */
    public function __construct()
    {
        $this->main = false;
        $this->requestedAt = new \DateTime();
        $this->state = StreamWorkflow::STATE_REQUESTED;
    }

    /**
     * @return string
     */
    public function getStreamerName()
    {
        return $this->organizer ? $this->organizer->getName() : $this->getUser()->getName();
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
     * Set channel
     *
     * @param string $channel
     *
     * @return Stream
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Stream
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
     * Set main
     *
     * @param boolean $main
     *
     * @return Stream
     */
    public function setMain($main)
    {
        $this->main = $main;

        return $this;
    }

    /**
     * Get main
     *
     * @return bool
     */
    public function getMain()
    {
        return $this->main;
    }

    /**
     * Set requestedAt
     *
     * @param \DateTime $requestedAt
     *
     * @return Stream
     */
    public function setRequestedAt($requestedAt)
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    /**
     * Get requestedAt
     *
     * @return \DateTime
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
    }

    /**
     * @return Tournament
     */
    public function getTournament()
    {
        return $this->tournament;
    }

    /**
     * @param Tournament $tournament
     *
     * @return Stream
     */
    public function setTournament(Tournament $tournament)
    {
        $this->tournament = $tournament;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Stream
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Organizer
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }

    /**
     * @param Organizer $organizer
     *
     * @return Stream
     */
    public function setOrganizer(Organizer $organizer = null)
    {
        $this->organizer = $organizer;

        return $this;
    }
}
