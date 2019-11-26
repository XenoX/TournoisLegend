<?php

namespace TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use TournamentBundle\Workflow\LanWorkflow;

/**
 * LanParticipant
 *
 * @ORM\Table(name="lan_participant")
 * @ORM\Entity()
 * @UniqueEntity("email")
 * @UniqueEntity("name")
 */
class LanParticipant
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
     * @Assert\Email()
     * @Assert\NotNull()
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

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
     *
     * @ORM\Column(name="players", type="string", length=255, nullable=true)
     */
    private $players;

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
     * @var \TournamentBundle\Entity\Lan
     *
     * @Assert\Valid()
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="TournamentBundle\Entity\Lan")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lan;

    /**
     * @return bool
     */
    public function isRegistered()
    {
        return LanWorkflow::STATE_REGISTERED === $this->state;
    }

    /**
     * @return bool
     */
    public function isConfirmed()
    {
        return LanWorkflow::STATE_CONFIRMED === $this->state;
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
     * Set email
     *
     * @param string $email
     *
     * @return LanParticipant
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return LanParticipant
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
     * Set players
     *
     * @param string $players
     *
     * @return LanParticipant
     */
    public function setPlayers($players)
    {
        $this->players = $players;

        return $this;
    }

    /**
     * Get players
     *
     * @return string
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return LanParticipant
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
     * Get lan
     *
     * @return Lan
     */
    public function getLan()
    {
        return $this->lan;
    }

    /**
     * Set lan
     *
     * @param Lan $lan
     *
     * @return LanParticipant
     */
    public function setLan($lan)
    {
        $this->lan = $lan;

        return $this;
    }
}
