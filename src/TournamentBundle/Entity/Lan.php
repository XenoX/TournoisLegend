<?php

namespace TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Game;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Lan
 *
 * @ORM\Table(name="lan")
 * @ORM\Entity
 */
class Lan
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

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
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="bracket", type="text", nullable=true)
     */
    private $bracket;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="bracket_amateur", type="text", nullable=true)
     */
    private $bracketAmateur;

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
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="plan", type="text")
     */
    private $plan;

    /**
     * @var int
     *
     * @Assert\Type("int")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="twitch", type="string", length=255, nullable=true)
     */
    private $twitch;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="dailymotion", type="string", length=255, nullable=true)
     */
    private $dailymotion;

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
     * @var bool
     *
     * @Assert\Type("bool")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="activated", type="boolean")
     */
    private $activated;

    /**
     * @var Description
     *
     * @Assert\Valid()
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="Description")
     */
    private $description;

    /**
     * @var Reward
     *
     * @Assert\Valid()
     *
     * @ORM\ManyToOne(targetEntity="Reward")
     */
    private $reward;

    /**
     * @var \AppBundle\Entity\Game
     * @Assert\Valid()
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Game")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @var Rules
     *
     * @Assert\Valid()
     *
     * @ORM\ManyToOne(targetEntity="Rules")
     */
    private $rules;

    /**
     * @var Mail
     *
     * @Assert\Valid()
     *
     * @ORM\ManyToOne(targetEntity="Mail")
     */
    private $mail;

    /**
     * Lan constructor.
     */
    public function __construct()
    {
        $this->price = 0;
        $this->activated = false;
    }

    /**
     * @return bool
     */
    public function isSolo()
    {
        return 1 === $this->format;
    }

    /**
     * @return bool
     */
    public function isTeam()
    {
        return 1 !== $this->format;
    }

    /**
     * @return bool
     */
    public function isFree()
    {
        return 0 === $this->price;
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
     * @return Lan
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Lan
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return Lan
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
     * Set description
     *
     * @param Description $description
     *
     * @return Lan
     */
    public function setDescription(Description $description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return Description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set bracket
     *
     * @param string $bracket
     *
     * @return Lan
     */
    public function setBracket($bracket)
    {
        $this->bracket = $bracket;

        return $this;
    }

    /**
     * Get bracket
     *
     * @return string
     */
    public function getBracket()
    {
        return $this->bracket;
    }

    /**
     * Set bracketAmateur
     *
     * @param string $bracketAmateur
     *
     * @return Lan
     */
    public function setBracketAmateur($bracketAmateur)
    {
        $this->bracketAmateur = $bracketAmateur;

        return $this;
    }

    /**
     * Get bracketAmateur
     *
     * @return string
     */
    public function getBracketAmateur()
    {
        return $this->bracketAmateur;
    }

    /**
     * Set format
     *
     * @param integer $format
     *
     * @return Lan
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
     * Set plan
     *
     * @param string $plan
     *
     * @return Lan
     */
    public function setPlan($plan)
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get plan
     *
     * @return string
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return Lan
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set twitch
     *
     * @param string $twitch
     *
     * @return Lan
     */
    public function setTwitch($twitch)
    {
        $this->twitch = $twitch;

        return $this;
    }

    /**
     * Get twitch
     *
     * @return string
     */
    public function getTwitch()
    {
        return $this->twitch;
    }

    /**
     * Set dailymotion
     *
     * @param string $dailymotion
     *
     * @return Lan
     */
    public function setDailymotion($dailymotion)
    {
        $this->dailymotion = $dailymotion;

        return $this;
    }

    /**
     * Get dailymotion
     *
     * @return string
     */
    public function getDailymotion()
    {
        return $this->dailymotion;
    }

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     *
     * @return Lan
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
     * Set registrationStartAt
     *
     * @param \DateTime $registrationStartAt
     *
     * @return Lan
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
     * @return Lan
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
     * Set activated
     *
     * @param boolean $activated
     *
     * @return Lan
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
     * @return Reward
     */
    public function getReward()
    {
        return $this->reward;
    }

    /**
     * @param Reward $reward
     *
     * @return Lan
     */
    public function setReward(Reward $reward = null)
    {
        $this->reward = $reward;

        return $this;
    }

    /**
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param Game $game
     *
     * @return Lan
     */
    public function setGame($game)
    {
        $this->game = $game;

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
     * @return Lan
     */
    public function setRules($rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * @return Mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param Mail $mail
     *
     * @return Lan
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }
}
