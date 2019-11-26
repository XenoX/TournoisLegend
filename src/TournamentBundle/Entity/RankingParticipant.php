<?php

namespace TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\Team;
use UserBundle\Entity\User;

/**
 * RankingParticipant
 *
 * @ORM\Table(name="ranking_participant")
 * @ORM\Entity(repositoryClass="TournamentBundle\Repository\RankingParticipantRepository")
 */
class RankingParticipant
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
     * @Assert\NotNull()
     *
     * @ORM\Column(name="elo", type="integer")
     */
    private $elo;

    /**
     * @ORM\ManyToOne(targetEntity="TournamentBundle\Entity\Ranking")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ranking;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Team")
     */
    private $team;

    /**
     * @return Team|User
     */
    public function getTeamOrUser()
    {
        return $this->team ?? $this->user;
    }

    /**
     * @return string
     */
    public function getTeamOrUserString()
    {
        return $this->team ? 'team' : 'user';
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
     * Set elo
     *
     * @param integer $elo
     *
     * @return RankingParticipant
     */
    public function setElo($elo)
    {
        $this->elo = $elo;

        return $this;
    }

    /**
     * Get elo
     *
     * @return int
     */
    public function getElo()
    {
        return $this->elo;
    }

    /**
     * Get ranking
     *
     * @return Ranking
     */
    public function getRanking()
    {
        return $this->ranking;
    }

    /**
     * @param Ranking $ranking
     *
     * @return RankingParticipant
     */
    public function setRanking(Ranking $ranking)
    {
        $this->ranking = $ranking;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param User|null $user
     *
     * @return RankingParticipant
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get team
     *
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set team
     *
     * @param Team|null $team
     *
     * @return RankingParticipant
     */
    public function setTeam(Team $team = null)
    {
        $this->team = $team;

        return $this;
    }
}

