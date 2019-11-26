<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameRepository")
 * @UniqueEntity("name")
 */
class Game
{
    const DEFAULT_LOGO = 'default.png';
    const DEFAULT_BANNER = 'default.jpg';

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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="short_name", type="string", length=255)
     */
    private $shortName;

    /**
     * @var bool
     *
     * @Assert\Type("boolean")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="team", type="boolean")
     */
    private $team;

    /**
     * @var bool
     *
     * @Assert\Type("boolean")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="solo", type="boolean")
     */
    private $solo;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="image", type="string", length=255)
     */
    private $logo;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="banner", type="string", length=255)
     */
    private $banner;

    /**
     * @var bool
     *
     * @Assert\Type("boolean")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="activated", type="boolean")
     */
    private $activated;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Mode", mappedBy="game")
     * @ORM\JoinColumn()
     */
    private $modes;

    /**
     * @Assert\File(
     *      maxSize="600K",
     *      mimeTypes = {"image/png", "image/jpeg"}
     * )
     */
    public $file;

    /**
     * @Assert\File(
     *      maxSize="1024K",
     *      mimeTypes = {"image/png", "image/jpeg"}
     * )
     */
    public $fileBanner;

    /**
     * Game constructor.
     */
    public function __construct()
    {
        $this->team = true;
        $this->solo = false;
        $this->logo = self::DEFAULT_LOGO;
        $this->banner = self::DEFAULT_BANNER;
        $this->activated = true;
        $this->modes = new ArrayCollection();
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
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        false === $this->activated ? $status = '[Disabled] ' : $status = '';
        return $status.$this->getShortName().' - '.$this->getName();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Game
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
     * Set shortName
     *
     * @param string $shortName
     *
     * @return Game
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set team
     *
     * @param boolean $team
     *
     * @return Game
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return bool
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set solo
     *
     * @param boolean $solo
     *
     * @return Game
     */
    public function setSolo($solo)
    {
        $this->solo = $solo;

        return $this;
    }

    /**
     * Get solo
     *
     * @return bool
     */
    public function getSolo()
    {
        return $this->solo;
    }

    /**
     * Set image
     *
     * @param string $logo
     *
     * @return Game
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set banner
     *
     * @param string $banner
     *
     * @return Game
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
     * @return Game
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
     * Add mode
     *
     * @param Mode $mode
     *
     * @return Game
     */
    public function addMode(Mode $mode)
    {
        $this->modes[] = $mode;

        return $this;
    }

    /**
     * Remove mode
     *
     * @param Mode $mode
     */
    public function removeMode(Mode $mode)
    {
        $this->modes->removeElement($mode);
    }

    /**
     * Get modes
     *
     * @return ArrayCollection
     */
    public function getModes()
    {
        return $this->modes;
    }

    /*
     ****************
     * LOGO UPLOAD
     ****************
     */
    public function getAbsolutePath()
    {
        if (self::DEFAULT_LOGO === $this->logo) {
            return __DIR__ . '/../../../web/static/img/logo-tournament-profile.jpg';
        }
        return $this->getUploadRootDir().'/'.$this->logo;
    }

    public function getWebPath()
    {
        if (self::DEFAULT_LOGO === $this->logo) {
            return 'static/img/logo-tournament-profile.jpg';
        }
        return $this->getUploadDir().'/'.$this->logo;
    }

    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../web/' .$this->getUploadDir();
    }

    public function getUploadDir()
    {
        return 'uploads/game/logo';
    }

    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        $this->logo = sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension();
        $this->file->move($this->getUploadRootDir(), $this->logo);

        unset($this->file);
    }

    /*
     ****************
     * BANNER UPLOAD
     ****************
     */
    public function getAbsolutePathBanner()
    {
        if (self::DEFAULT_BANNER === $this->banner) {
            return __DIR__ . '/../../../web/static/img/cover-tournament-profile.jpg';
        }
        return $this->getUploadRootDirBanner().'/'.$this->banner;
    }

    public function getWebPathBanner()
    {
        if (self::DEFAULT_BANNER === $this->banner) {
            return 'static/img/cover-tournament-profile.jpg';
        }
        return $this->getUploadDirBanner().'/'.$this->banner;
    }

    protected function getUploadRootDirBanner()
    {
        return __DIR__ . '/../../../web/' .$this->getUploadDirBanner();
    }

    protected function getUploadDirBanner()
    {
        return 'uploads/game/banner';
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
