<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use TournamentBundle\Workflow\ParticipantWorkflow;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\TeamRepository")
 * @UniqueEntity("name")
 */
class Team
{
    const DEFAULT_LOGO = 'default.jpg';
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
     * @Assert\Length(min = 3, max = 30)
     *
     * @ORM\Column(name="name", type="string", length=30, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\Type("alnum")
     * @Assert\Length(min = 2, max = 6)
     *
     * @ORM\Column(name="tag", type="string", length=6)
     */
    private $tag;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\Length(max="300")
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

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
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="banner", type="string", length=255)
     */
    private $banner;

    /**
     * @var string
     *
     * @Assert\Url(
     *     protocols = {"http", "https"}
     * )
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;

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
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var string
     *
     * @Assert\Regex("/https:\/\/(www\.)?twitch\.tv\/[A-z0-9_-]+/")
     *
     * @ORM\Column(name="twitch", type="string", length=255, nullable=true)
     */
    private $twitch;

    /**
     * @var string
     *
     * @Assert\Regex("/https:\/\/(www\.)?(facebook|fb)\.com\/[A-z0-9_-]+/")
     *
     * @ORM\Column(name="facebook", type="string", length=255, nullable=true)
     */
    private $facebook;

    /**
     * @var string
     *
     * @Assert\Regex("/https:\/\/(.*\.)?twitter\.com\/[A-z0-9_]+\/?/")
     *
     * @ORM\Column(name="twitter", type="string", length=255, nullable=true)
     */
    private $twitter;

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
     * @var bool
     *
     * @Assert\Type("bool")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $leader;

    /**
     * @Assert\File(
     *      maxSize="200K",
     *      mimeTypes = {"image/png", "image/jpeg"}
     * )
     */
    public $file;

    /**
     * @Assert\File(
     *      maxSize="800K",
     *      mimeTypes = {"image/png", "image/jpeg"}
     * )
     */
    public $fileBanner;

    /**
     * Team constructor.
     */
    public function __construct()
    {
        $this->logo = self::DEFAULT_LOGO;
        $this->banner = self::DEFAULT_BANNER;
        $this->createdAt = new \DateTime();
        $this->activated = true;
        $this->deleted = false;
        $this->generateToken();
    }

    public function generateToken()
    {
        $this->token = md5(uniqid(null, true));
    }

    /**
     * @return bool
     */
    public function isNonDeleted()
    {
        return !$this->deleted;
    }

    /**
     * @return bool
     */
    public function isClean()
    {
        return $this->isNonDeleted() && $this->getActivated();
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
     * @return Team
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
     * Set tag
     *
     * @param string $tag
     *
     * @return Team
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Team
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set logo
     *
     * @param string $logo
     *
     * @return Team
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
     * Set banner
     *
     * @param string $banner
     *
     * @return Team
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
     * Set website
     *
     * @param string $website
     *
     * @return Team
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Team
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
     * Set token
     *
     * @param string $token
     *
     * @return Team
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set twitch
     *
     * @param string $twitch
     *
     * @return Team
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
     * Set facebook
     *
     * @param string $facebook
     *
     * @return Team
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     *
     * @return Team
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set activated
     *
     * @param boolean $activated
     *
     * @return Team
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
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Team
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return bool
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set leader
     *
     * @param \UserBundle\Entity\User $leader
     *
     * @return Team
     */
    public function setLeader(\UserBundle\Entity\User $leader)
    {
        $this->leader = $leader;

        return $this;
    }

    /**
     * Get leader
     *
     * @return \UserBundle\Entity\User
     */
    public function getLeader()
    {
        return $this->leader;
    }

    /*
     ****************
     * LOGO UPLOAD
     ****************
     */
    public function getAbsolutePath()
    {
        if (self::DEFAULT_LOGO === $this->logo) {
            return __DIR__.'/../../../web/static/img/logo-team.png';
        }
        return $this->getUploadRootDir().'/'.$this->logo;
    }

    public function getWebPath()
    {
        if (self::DEFAULT_LOGO === $this->logo) {
            return 'static/img/logo-team.png';
        }
        return $this->getUploadDir().'/'.$this->logo;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    public function getUploadDir()
    {
        return 'uploads/team/logo';
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
            return __DIR__.'/../../../web/static/img/cover-team-profile.jpg';
        }
        return $this->getUploadRootDirBanner().'/'.$this->banner;
    }

    public function getWebPathBanner()
    {
        if (self::DEFAULT_BANNER === $this->banner) {
            return 'static/img/cover-team-profile.jpg';
        }
        return $this->getUploadDirBanner().'/'.$this->banner;
    }

    protected function getUploadRootDirBanner()
    {
        return __DIR__.'/../../../web/'.$this->getUploadDirBanner();
    }

    protected function getUploadDirBanner()
    {
        return 'uploads/user/banner';
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
