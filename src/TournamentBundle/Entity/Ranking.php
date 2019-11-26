<?php

namespace TournamentBundle\Entity;

use AppBundle\Entity\Mode;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ranking
 *
 * @ORM\Table(name="ranking")
 * @ORM\Entity(repositoryClass="TournamentBundle\Repository\RankingRepository")
 */
class Ranking
{
    const BASE_ELO = 500;

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
     * @ORM\Column(name="name_fr", type="string", length=255)
     */
    private $nameFr;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="name_en", type="string", length=255)
     */
    private $nameEn;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @Assert\NotNull()
     *
     * @ORM\Column(name="start_at", type="datetime", nullable=true)
     */
    private $startAt;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @Assert\NotNull()
     *
     * @ORM\Column(name="stop_at", type="datetime", nullable=true)
     */
    private $stopAt;

    /**
     * @var int
     *
     * @Assert\Type("int")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="base_elo", type="integer")
     */
    private $baseElo;

    /**
     * @var int
     *
     * @Assert\Type("int")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="season_number", type="integer", nullable=true)
     */
    private $seasonNumber;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Mode")
     */
    private $mode;

    /**
     * Ranking constructor.
     */
    public function __construct()
    {
        $this->baseElo = self::BASE_ELO;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getNameEn();
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
     * Set nameFr
     *
     * @param string $nameFr
     *
     * @return Ranking
     */
    public function setNameFr($nameFr)
    {
        $this->nameFr = $nameFr;

        return $this;
    }

    /**
     * Get nameFr
     *
     * @return string
     */
    public function getNameFr()
    {
        return $this->nameFr;
    }

    /**
     * Set nameEn
     *
     * @param string $nameEn
     *
     * @return Ranking
     */
    public function setNameEn($nameEn)
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    /**
     * Get nameEn
     *
     * @return string
     */
    public function getNameEn()
    {
        return $this->nameEn;
    }

    /**
     * Set startAt
     *
     * @param \DateTime|null $startAt
     *
     * @return Ranking
     */
    public function setStartAt(\DateTime $startAt = null)
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
     * Set stopAt
     *
     * @param \DateTime|null $stopAt
     *
     * @return Ranking
     */
    public function setStopAt(\DateTime $stopAt = null)
    {
        $this->stopAt = $stopAt;

        return $this;
    }

    /**
     * Get stopAt
     *
     * @return \DateTime
     */
    public function getStopAt()
    {
        return $this->stopAt;
    }

    /**
     * Set baseElo
     *
     * @param int $baseElo
     *
     * @return Ranking
     */
    public function setBaseElo($baseElo)
    {
        $this->baseElo = $baseElo;

        return $this;
    }

    /**
     * Get baseElo
     *
     * @return int
     */
    public function getBaseElo()
    {
        return $this->baseElo;
    }

    /**
     * Set seasonNumber
     *
     * @param int|null $seasonNumber
     *
     * @return Ranking
     */
    public function setSeasonNumber($seasonNumber = null)
    {
        $this->seasonNumber = $seasonNumber;

        return $this;
    }

    /**
     * Get seasonNumber
     *
     * @return int
     */
    public function getSeasonNumber()
    {
        return $this->seasonNumber;
    }

    /**
     * Get mode
     *
     * @return Mode
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set mode
     *
     * @param Mode $mode
     *
     * @return Ranking
     */
    public function setMode(Mode $mode)
    {
        $this->mode = $mode;

        return $this;
    }
}

