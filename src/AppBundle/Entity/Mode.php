<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TournamentBundle\Entity\Tournament;

/**
 * Mode
 *
 * @ORM\Table(name="mode")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModeRepository")
 */
class Mode
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
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="banner", type="string", length=255, nullable=true)
     */
    private $banner;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Game", inversedBy="modes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TournamentBundle\Entity\Tournament", mappedBy="mode")
     * @ORM\JoinColumn()
     */
    private $tournaments;

    /**
     * @Assert\File(
     *      maxSize="1024K",
     *      mimeTypes = {"image/png", "image/jpeg"}
     * )
     */
    public $fileBanner;

    /**
     * Mode constructor.
     */
    public function __construct()
    {
        $this->activated = true;
        $this->tournaments = new ArrayCollection();
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
     * @return string
     */
    public function getFullName()
    {
        return $this->getGame()->getShortName().' - '.$this->getName();
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
     * Set nameFr
     *
     * @param string $nameFr
     *
     * @return Mode
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
     * @return Mode
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
     * Set banner
     *
     * @param string $banner
     *
     * @return Mode
     */
    public function setBanner($banner)
    {
        $this->banner = $banner;

        return $this;
    }

    /**
     * Get banner
     *
     * @return string
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * Set activated
     *
     * @param boolean $activated
     *
     * @return Mode
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
     * Set game
     *
     * @param Game $game
     *
     * @return Mode
     */
    public function setGame(Game $game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Add tournament
     *
     * @param Tournament $tournament
     *
     * @return Mode
     */
    public function addTournament(Tournament $tournament)
    {
        $this->tournaments[] = $tournament;

        return $this;
    }

    /**
     * Remove tournament
     *
     * @param Tournament $tournament
     */
    public function removeTournament(Tournament $tournament)
    {
        $this->tournaments->removeElement($tournament);
    }

    /**
     * Get tournaments
     *
     * @return ArrayCollection
     */
    public function getTournaments()
    {
        return $this->tournaments;
    }

    /*
     ****************
     * BANNER UPLOAD
     ****************
     */
    public function getAbsolutePathBanner()
    {
        if (null === $this->banner) {
            return null;
        }

        return $this->getUploadRootDirBanner().'/'.$this->banner;
    }

    public function getWebPathBanner()
    {
        if (null === $this->banner) {
            return null;
        }

        return $this->getUploadDirBanner().'/'.$this->banner;
    }

    protected function getUploadRootDirBanner()
    {
        return __DIR__ . '/../../../web/' .$this->getUploadDirBanner();
    }

    protected function getUploadDirBanner()
    {
        return 'uploads/mode/banner';
    }

    public function uploadBanner()
    {
        if (null === $this->fileBanner) {
            return;
        }

        $this->banner = sha1(uniqid(mt_rand(), true)).'.'.$this->fileBanner->guessExtension();
        $this->fileBanner->move($this->getUploadRootDirBanner(), $this->banner);

        unset($this->fileBanner);
    }
}
