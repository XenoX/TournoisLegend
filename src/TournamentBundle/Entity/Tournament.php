<?php

namespace TournamentBundle\Entity;

use AppBundle\Entity\Game;
use AppBundle\Entity\Mode;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TournamentBundle\Workflow\TournamentWorkflow;

/**
 * Tournament
 *
 * @ORM\Table(name="tournament")
 * @ORM\Entity(repositoryClass="TournamentBundle\Repository\TournamentRepository")
 */
class Tournament
{
    const PRIZE_REASON_SMALL = 'prize.reason.small';
    const PRIZE_REASON_CANCEL = 'prize.reason.cancel';
    const PRIZE_REASON_OTHER = 'prize.reason.other';
    const MINIMUM_PARTICIPANT = 4;

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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @Assert\Type("int")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="size", type="integer")
     */
    private $size;

    /**
     * @var int
     *
     * @Assert\Type("int")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="format", type="integer")
     */
    private $format;

    /**
     * @var float
     *
     * @Assert\Type("float")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="ranking_ratio", type="float")
     */
    private $rankingRatio;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="map_name_fr", type="string", length=255, nullable=true)
     */
    private $mapNameFr;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="map_name_en", type="string", length=255, nullable=true)
     */
    private $mapNameEn;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="prize_reason", type="string", length=255, nullable=true)
     */
    private $prizeReason;

    /**
     * @var int
     *
     * @Assert\Type("int")
     *
     * @ORM\Column(name="riot_id", type="integer", nullable=true)
     */
    private $riotId;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @Assert\NotNull()
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @Assert\NotNull()
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @Assert\NotNull()
     *
     * @ORM\Column(name="registration_start_at", type="datetime")
     */
    private $registrationStartAt;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @Assert\NotNull()
     *
     * @ORM\Column(name="registration_stop_at", type="datetime")
     */
    private $registrationStopAt;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @Assert\NotNull()
     *
     * @ORM\Column(name="start_at", type="datetime")
     */
    private $startAt;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="streams", type="boolean")
     */
    private $streams;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="hidden_participant", type="boolean")
     */
    private $hiddenParticipant;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="started", type="boolean")
     */
    private $started;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="start_auto", type="boolean")
     */
    private $startAuto;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="activated", type="boolean")
     */
    private $activated;

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
     * @var Mode
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Mode", inversedBy="tournaments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mode;

    /**
     * @var Description
     *
     * @ORM\ManyToOne(targetEntity="TournamentBundle\Entity\Description")
     * @ORM\JoinColumn(nullable=false)
     */
    private $description;

    /**
     * @var Reward
     *
     * @ORM\ManyToOne(targetEntity="TournamentBundle\Entity\Reward")
     */
    private $reward;

    /**
     * @var Rules
     *
     * @ORM\ManyToOne(targetEntity="TournamentBundle\Entity\Rules")
     */
    private $rules;

    /**
     * @var Organizer
     *
     * @ORM\ManyToOne(targetEntity="TournamentBundle\Entity\Organizer", inversedBy="tournaments")
     */
    private $organizer;

    /**
     * Tournament constructor.
     */
    public function __construct()
    {
        $this->size = 32;
        $this->format = 5;
        $this->rankingRatio = 1;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->startAt = new \DateTime('+7 day');
        $this->registrationStopAt = new \DateTime('+7 day');
        $this->streams = true;
        $this->hiddenParticipant = false;
        $this->started = false;
        $this->startAuto = true;
        $this->activated = true;
        $this->state =  TournamentWorkflow::STATE_INIT;
    }

    /**
     * @return bool
     */
    public function isTeam()
    {
        return 1 !== $this->getFormat();
    }

    /**
     * @return bool
     */
    public function isSolo()
    {
        return 1 === $this->getFormat();
    }

    /**
     * @return bool
     */
    public function isRegistrationDatetimePhase()
    {
        $now = (new \DateTime())->getTimestamp();

        return $now >= $this->registrationStartAt->getTimestamp() && $now <= $this->registrationStopAt->getTimestamp();
    }

    /**
     * @return bool
     */
    public function isCheckInDatetimePhase()
    {
        $now = (new \DateTime())->getTimestamp();

        return $now >= $this->registrationStopAt->getTimestamp() && false === $this->started;
    }

    /**
     * @return bool
     */
    public function canRegistration()
    {
        return TournamentWorkflow::STATE_REGISTRATION === $this->state || TournamentWorkflow::STATE_CHECK_IN === $this->state;
    }

    /**
     * @return bool
     */
    public function isRegistrationPhase()
    {
        return TournamentWorkflow::STATE_REGISTRATION === $this->state;
    }

    /**
     * @return bool
     */
    public function isCheckInPhase()
    {
        return TournamentWorkflow::STATE_CHECK_IN === $this->state;
    }

    /**
     * @return bool
     */
    public function isDone()
    {
        return TournamentWorkflow::STATE_DONE === $this->state;
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
     * Set name
     *
     * @param string $name
     *
     * @return Tournament
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set size
     *
     * @param integer $size
     *
     * @return Tournament
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set format
     *
     * @param integer $format
     *
     * @return Tournament
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormatStringify()
    {
        return $this->format.'vs'.$this->format;
    }

    /**
     * Get format
     *
     * @return int
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set rankingRatio
     *
     * @param float $rankingRatio
     *
     * @return Tournament
     */
    public function setRankingRatio($rankingRatio)
    {
        $this->rankingRatio = $rankingRatio;

        return $this;
    }

    /**
     * Get rankingRatio
     *
     * @return float
     */
    public function getRankingRatio()
    {
        return $this->rankingRatio;
    }

    /**
     * Set mapNameFr
     *
     * @param string $mapNameFr
     *
     * @return Tournament
     */
    public function setMapNameFr($mapNameFr)
    {
        $this->mapNameFr = $mapNameFr;

        return $this;
    }

    /**
     * Get mapNameFr
     *
     * @return string
     */
    public function getMapNameFr()
    {
        return $this->mapNameFr;
    }

    /**
     * Set mapNameEn
     *
     * @param string $mapNameEn
     *
     * @return Tournament
     */
    public function setMapNameEn($mapNameEn)
    {
        $this->mapNameEn = $mapNameEn;

        return $this;
    }

    /**
     * Get mapNameEn
     *
     * @return string
     */
    public function getMapNameEn()
    {
        return $this->mapNameEn;
    }

    /**
     * Set prizeReason
     *
     * @param string $prizeReason
     *
     * @return Tournament
     */
    public function setPrizeReason($prizeReason)
    {
        $this->prizeReason = $prizeReason;

        return $this;
    }

    /**
     * Get prizeReason
     *
     * @return string
     */
    public function getPrizeReason()
    {
        return $this->prizeReason;
    }

    /**
     * Set riotId
     *
     * @param integer $riotId
     *
     * @return Tournament
     */
    public function setRiotId($riotId)
    {
        $this->riotId = $riotId;

        return $this;
    }

    /**
     * Get riotId
     *
     * @return int
     */
    public function getRiotId()
    {
        return $this->riotId;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Tournament
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Tournament
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set registrationStartAt
     *
     * @param \DateTime $registrationStartAt
     *
     * @return Tournament
     */
    public function setRegistrationStartAt($registrationStartAt)
    {
        $this->registrationStartAt = $registrationStartAt;

        return $this;
    }

    /**
     * Get registrationStartAt
     *
     * @return \DateTime
     */
    public function getRegistrationStartAt()
    {
        return $this->registrationStartAt;
    }

    /**
     * Set registrationStopAt
     *
     * @param \DateTime $registrationStopAt
     *
     * @return Tournament
     */
    public function setRegistrationStopAt($registrationStopAt)
    {
        $this->registrationStopAt = $registrationStopAt;

        return $this;
    }

    /**
     * Get registrationStopAt
     *
     * @return \DateTime
     */
    public function getRegistrationStopAt()
    {
        return $this->registrationStopAt;
    }

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     *
     * @return Tournament
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * @return bool
     */
    public function getStreams(): bool
    {
        return $this->streams;
    }

    /**
     * @param bool $streams
     *
     * @return Tournament
     */
    public function setStreams(bool $streams): Tournament
    {
        $this->streams = $streams;

        return $this;
    }

    /**
     * Set hiddenParticipant
     *
     * @param boolean $hiddenParticipant
     *
     * @return Tournament
     */
    public function setHiddenParticipant($hiddenParticipant)
    {
        $this->hiddenParticipant = $hiddenParticipant;

        return $this;
    }

    /**
     * Get hiddenParticipant
     *
     * @return bool
     */
    public function getHiddenParticipant()
    {
        return $this->hiddenParticipant;
    }

    /**
     * Set started
     *
     * @param boolean $started
     *
     * @return Tournament
     */
    public function setStarted($started)
    {
        $this->started = $started;

        return $this;
    }

    /**
     * Is started
     *
     * @return bool
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Set startAuto
     *
     * @param boolean $startAuto
     *
     * @return Tournament
     */
    public function setStartAuto($startAuto)
    {
        $this->startAuto = $startAuto;

        return $this;
    }

    /**
     * Get startAuto
     *
     * @return bool
     */
    public function getStartAuto()
    {
        return $this->startAuto;
    }

    /**
     * Set activated
     *
     * @param boolean $activated
     *
     * @return Tournament
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;

        return $this;
    }

    /**
     * Get activated
     *
     * @return bool
     */
    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Tournament
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
     * @return Mode
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param Mode $mode
     *
     * @return Tournament
     */
    public function setMode(Mode $mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return Description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param Description $description
     *
     * @return Tournament
     */
    public function setDescription(Description $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Reward
     */
    public function getReward()
    {
        return $this->reward;
    }

    /**
     * @param Reward $reward
     *
     * @return Tournament
     */
    public function setReward(Reward $reward = null)
    {
        $this->reward = $reward;

        return $this;
    }

    /**
     * @return Rules
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param Rules $rules
     *
     * @return Tournament
     */
    public function setRules(Rules $rules = null)
    {
        $this->rules = $rules;

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
     * @return Tournament
     */
    public function setOrganizer(Organizer $organizer = null)
    {
        $this->organizer = $organizer;

        return $this;
    }
}
