<?php

namespace TournamentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Organizer
 *
 * @ORM\Table(name="organizer")
 * @ORM\Entity()
 * @UniqueEntity("name")
 */
class Organizer
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

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
     *
     * @ORM\Column(name="description_fr", type="text", nullable=true)
     */
    private $descriptionFr;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="description_en", type="text", nullable=true)
     */
    private $descriptionEn;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @var ArrayCollection
     *
     * @Assert\Valid()
     *
     * @ORM\OneToMany(targetEntity="Tournament", mappedBy="organizer")
     * @ORM\JoinColumn()
     */
    private $tournaments;

    /**
     * @Assert\File(
     *      maxSize="600K",
     *      mimeTypes = {"image/png", "image/jpeg"}
     * )
     */
    public $file;

    /**
     * Organizer constructor.
     */
    public function __construct()
    {
        $this->logo = self::DEFAULT_LOGO;
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
     * Set name
     *
     * @param string $name
     *
     * @return Organizer
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
     * Set logo
     *
     * @param string $logo
     *
     * @return Organizer
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
     * Set descriptionFr
     *
     * @param string $descriptionFr
     *
     * @return Organizer
     */
    public function setDescriptionFr($descriptionFr)
    {
        $this->descriptionFr = $descriptionFr;

        return $this;
    }

    /**
     * Get descriptionFr
     *
     * @return string
     */
    public function getDescriptionFr()
    {
        return $this->descriptionFr;
    }

    /**
     * Set descriptionEn
     *
     * @param string $descriptionEn
     *
     * @return Organizer
     */
    public function setDescriptionEn($descriptionEn)
    {
        $this->descriptionEn = $descriptionEn;

        return $this;
    }

    /**
     * Get descriptionEn
     *
     * @return string
     */
    public function getDescriptionEn()
    {
        return $this->descriptionEn;
    }

    /**
     * Set website
     *
     * @param string $website
     *
     * @return Organizer
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
     * @param Tournament $tournament
     *
     * @return Organizer
     */
    public function addTournament($tournament)
    {
        $this->tournaments[] = $tournament;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTournaments()
    {
        return $this->tournaments;
    }

    /**
     * @param ArrayCollection $tournaments
     *
     * @return Organizer
     */
    public function setTournaments($tournaments)
    {
        $this->tournaments = $tournaments;

        return $this;
    }

    /*
     ****************
     * LOGO UPLOAD
     ****************
     */

    /**
     * @return string
     */
    public function getAbsolutePath()
    {
        if (self::DEFAULT_LOGO === $this->logo) {
            return __DIR__.'/../../../web/static/img/organizer/'.self::DEFAULT_LOGO;
        }
        return $this->getUploadRootDir().'/'.$this->logo;
    }

    /**
     * @return string
     */
    public function getWebPath()
    {
        if (self::DEFAULT_LOGO === $this->logo) {
            return 'static/img/organizer/'.self::DEFAULT_LOGO;
        }
        return $this->getUploadDir().'/'.$this->logo;
    }

    /**
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    /**
     * @return string
     */
    public function getUploadDir()
    {
        return 'uploads/organizer/logo';
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
}
