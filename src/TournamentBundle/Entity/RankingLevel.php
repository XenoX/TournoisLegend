<?php

namespace TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RankingLevel
 *
 * @ORM\Table(name="ranking_level")
 * @ORM\Entity(repositoryClass="TournamentBundle\Repository\RankingLevelRepository")
 */
class RankingLevel
{
    const DEFAULT_LOGO = 'default.png';

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
     * @var int
     *
     * @Assert\Type("int")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="elo_max", type="integer")
     */
    private $eloMax;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="color", type="string", length=255)
     */
    private $color;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="logo", type="string", length=255)
     */
    private $logo;

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
     * @Assert\File(
     *      maxSize="200K",
     *      mimeTypes = {"image/png", "image/jpeg"}
     * )
     */
    public $file;

    /**
     * RankingLevel constructor.
     */
    public function __construct()
    {
        $this->color = 'black';
        $this->logo = self::DEFAULT_LOGO;
        $this->activated = true;
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
     * @return RankingLevel
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
     * @return RankingLevel
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
     * Get eloMax
     *
     * @return int
     */
    public function getEloMax(): int
    {
        return $this->eloMax;
    }

    /**
     * set eloMax
     *
     * @param int $eloMax
     *
     * @return RankingLevel
     */
    public function setEloMax(int $eloMax)
    {
        $this->eloMax = $eloMax;

        return $this;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return RankingLevel
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set logo
     *
     * @param string $logo
     *
     * @return RankingLevel
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set activated
     *
     * @param boolean $activated
     *
     * @return RankingLevel
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

    /*
     ****************
     * LOGO UPLOAD
     ****************
     */
    public function getAbsolutePath()
    {
        if (self::DEFAULT_LOGO === $this->logo) {
            return __DIR__ . '/../../../web/static/img/ranking/default.png';
        }

        return $this->getUploadRootDir().'/'.$this->logo;
    }

    public function getWebPath()
    {
        if (self::DEFAULT_LOGO === $this->logo) {
            return 'static/img/ranking/default.png';
        }

        return $this->getUploadDir().'/'.$this->logo;
    }

    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../web/' .$this->getUploadDir();
    }

    public function getUploadDir()
    {
        return 'uploads/ranking/level';
    }

    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        $this->logo = preg_replace('/\s+/', '', strtolower($this->nameEn)).'-'.sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension();
        $this->file->move($this->getUploadRootDir(), $this->logo);

        unset($this->file);
    }
}

