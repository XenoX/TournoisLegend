<?php

namespace TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Battle
 *
 * @ORM\Table(name="battle")
 * @ORM\Entity(repositoryClass="TournamentBundle\Repository\BattleRepository")
 */
class Battle
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
     * @var int
     *
     * @Assert\Type("int")
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\NotNull()
     *
     * @ORM\Column(name="battle_id", type="integer")
     */
    private $battleId;

    /**
     * @var int
     *
     * @Assert\Type("int")
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\NotNull()
     *
     * @ORM\Column(name="round", type="integer")
     */
    private $round;

    /**
     * @var int
     *
     * @Assert\Type("int")
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\NotNull()
     *
     * @ORM\Column(name="score_1", type="smallint")
     */
    private $score1;

    /**
     * @var int
     *
     * @Assert\Type("int")
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\NotNull()
     *
     * @ORM\Column(name="score_2", type="smallint")
     */
    private $score2;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="contested", type="boolean")
     */
    private $contested;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

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
     *
     * @ORM\Column(name="end_at", type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * @var Participant
     *
     * @Assert\Valid()
     *
     * @ORM\ManyToOne(targetEntity="Participant")
     * @ORM\JoinColumn(name="participant_1_id")
     */
    private $participant1;

    /**
     * @var Participant
     *
     * @Assert\Valid()
     *
     * @ORM\ManyToOne(targetEntity="Participant")
     * @ORM\JoinColumn(name="participant_2_id")
     */
    private $participant2;

    /**
     * @var Tournament
     *
     * @Assert\Valid()
     *
     * @ORM\ManyToOne(targetEntity="Tournament")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tournament;

    public function __construct()
    {
        $this->score1 = 0;
        $this->score2 = 0;
        $this->contested = false;
        $this->startAt = new \DateTime();
    }

    /**
     * @return Participant
     */
    public function getWinner()
    {
        return $this->score1 > $this->score2 ? $this->participant1 : $this->participant2;
    }

    /**
     * @return Participant
     */
    public function getLoser()
    {
        return $this->score1 > $this->score2 ? $this->participant2 : $this->participant1;
    }

    /**
     * @return bool
     */
    public function isReady()
    {
        return $this->participant1 && $this->participant2;
    }

    /**
     * @return bool
     */
    public function isDone()
    {
        return $this->score1 !== $this->score2;
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
     * Set battleId
     *
     * @param integer $battleId
     *
     * @return Battle
     */
    public function setBattleId($battleId)
    {
        $this->battleId = $battleId;

        return $this;
    }

    /**
     * Get battleId
     *
     * @return int
     */
    public function getBattleId()
    {
        return $this->battleId;
    }

    /**
     * Set round
     *
     * @param integer $round
     *
     * @return Battle
     */
    public function setRound($round)
    {
        $this->round = $round;

        return $this;
    }

    /**
     * Get round
     *
     * @return int
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Set score1
     *
     * @param integer $score1
     *
     * @return Battle
     */
    public function setScore1($score1)
    {
        $this->score1 = $score1;

        return $this;
    }

    /**
     * Get score1
     *
     * @return int
     */
    public function getScore1()
    {
        return $this->score1;
    }

    /**
     * Set score2
     *
     * @param integer $score2
     *
     * @return Battle
     */
    public function setScore2($score2)
    {
        $this->score2 = $score2;

        return $this;
    }

    /**
     * Get score2
     *
     * @return int
     */
    public function getScore2()
    {
        return $this->score2;
    }

    /**
     * Set contested
     *
     * @param boolean $contested
     *
     * @return Battle
     */
    public function setContested($contested)
    {
        $this->contested = $contested;

        return $this;
    }

    /**
     * Get contested
     *
     * @return bool
     */
    public function getContested()
    {
        return $this->contested;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Battle
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     *
     * @return Battle
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
     * Set endAt
     *
     * @param \DateTime $endAt
     *
     * @return Battle
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * Set participant1
     *
     * @param Participant $participant1
     *
     * @return Battle
     */
    public function setParticipant1(Participant $participant1 = null)
    {
        $this->participant1 = $participant1;

        return $this;
    }

    /**
     * Get participant1
     *
     * @return Participant
     */
    public function getParticipant1()
    {
        return $this->participant1;
    }

    /**
     * Set participant2
     *
     * @param Participant $participant2
     *
     * @return Battle
     */
    public function setParticipant2(Participant $participant2 = null)
    {
        $this->participant2 = $participant2;

        return $this;
    }

    /**
     * Get participant2
     *
     * @return Participant
     */
    public function getParticipant2()
    {
        return $this->participant2;
    }

    /**
     * Set tournament
     *
     * @param Tournament $tournament
     *
     * @return Battle
     */
    public function setTournament(Tournament $tournament = null)
    {
        $this->tournament = $tournament;

        return $this;
    }

    /**
     * Get tournament
     *
     * @return Tournament
     */
    public function getTournament()
    {
        return $this->tournament;
    }
}
