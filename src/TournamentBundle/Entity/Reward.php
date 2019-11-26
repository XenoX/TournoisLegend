<?php

namespace TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reward
 *
 * @ORM\Table(name="reward")
 * @ORM\Entity()
 */
class Reward
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
     *
     * @ORM\Column(name="first_fr", type="string", length=255)
     */
    private $firstFr;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="first_en", type="string", length=255)
     */
    private $firstEn;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="second_fr", type="string", length=255, nullable=true)
     */
    private $secondFr;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="second_en", type="string", length=255, nullable=true)
     */
    private $secondEn;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="third_fr", type="string", length=255, nullable=true)
     */
    private $thirdFr;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="third_en", type="string", length=255, nullable=true)
     */
    private $thirdEn;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="fourth_fr", type="string", length=255, nullable=true)
     */
    private $fourthFr;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="fourth_en", type="string", length=255, nullable=true)
     */
    private $fourthEn;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="fifth_to_eighth_fr", type="string", length=255, nullable=true)
     */
    private $fifthToEighthFr;

    /**
     * @var string
     *
     * @Assert\Type("string")
     *
     * @ORM\Column(name="fifth_to_eighth_en", type="string", length=255, nullable=true)
     */
    private $fifthToEighthEn;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="lan", type="boolean")
     */
    private $lan;

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
     * Reward constructor.
     */
    public function __construct()
    {
        $this->lan = false;
        $this->activated = true;
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
        return $status.$this->getName();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Reward
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
     * Set firstFr
     *
     * @param string $firstFr
     *
     * @return Reward
     */
    public function setFirstFr($firstFr)
    {
        $this->firstFr = $firstFr;

        return $this;
    }

    /**
     * Get firstFr
     *
     * @return string
     */
    public function getFirstFr()
    {
        return $this->firstFr;
    }

    /**
     * Set firstEn
     *
     * @param string $firstEn
     *
     * @return Reward
     */
    public function setFirstEn($firstEn)
    {
        $this->firstEn = $firstEn;

        return $this;
    }

    /**
     * Get firstEn
     *
     * @return string
     */
    public function getFirstEn()
    {
        return $this->firstEn;
    }

    /**
     * Set secondFr
     *
     * @param string $secondFr
     *
     * @return Reward
     */
    public function setSecondFr($secondFr)
    {
        $this->secondFr = $secondFr;

        return $this;
    }

    /**
     * Get secondFr
     *
     * @return string
     */
    public function getSecondFr()
    {
        return $this->secondFr;
    }

    /**
     * Set secondEn
     *
     * @param string $secondEn
     *
     * @return Reward
     */
    public function setSecondEn($secondEn)
    {
        $this->secondEn = $secondEn;

        return $this;
    }

    /**
     * Get secondEn
     *
     * @return string
     */
    public function getSecondEn()
    {
        return $this->secondEn;
    }

    /**
     * Set thirdFr
     *
     * @param string $thirdFr
     *
     * @return Reward
     */
    public function setThirdFr($thirdFr)
    {
        $this->thirdFr = $thirdFr;

        return $this;
    }

    /**
     * Get thirdFr
     *
     * @return string
     */
    public function getThirdFr()
    {
        return $this->thirdFr;
    }

    /**
     * Set thirdEn
     *
     * @param string $thirdEn
     *
     * @return Reward
     */
    public function setThirdEn($thirdEn)
    {
        $this->thirdEn = $thirdEn;

        return $this;
    }

    /**
     * Get thirdEn
     *
     * @return string
     */
    public function getThirdEn()
    {
        return $this->thirdEn;
    }

    /**
     * Set fourthFr
     *
     * @param string $fourthFr
     *
     * @return Reward
     */
    public function setFourthFr($fourthFr)
    {
        $this->fourthFr = $fourthFr;

        return $this;
    }

    /**
     * Get fourthFr
     *
     * @return string
     */
    public function getFourthFr()
    {
        return $this->fourthFr;
    }

    /**
     * Set fourthEn
     *
     * @param string $fourthEn
     *
     * @return Reward
     */
    public function setFourthEn($fourthEn)
    {
        $this->fourthEn = $fourthEn;

        return $this;
    }

    /**
     * Get fourthEn
     *
     * @return string
     */
    public function getFourthEn()
    {
        return $this->fourthEn;
    }

    /**
     * Set fifthToEighthFr
     *
     * @param string $fifthToEighthFr
     *
     * @return Reward
     */
    public function setFifthToEighthFr($fifthToEighthFr)
    {
        $this->fifthToEighthFr = $fifthToEighthFr;

        return $this;
    }

    /**
     * Get fifthToEighthFr
     *
     * @return string
     */
    public function getFifthToEighthFr()
    {
        return $this->fifthToEighthFr;
    }

    /**
     * Set fifthToEighthEn
     *
     * @param string $fifthToEighthEn
     *
     * @return Reward
     */
    public function setFifthToEighthEn($fifthToEighthEn)
    {
        $this->fifthToEighthEn = $fifthToEighthEn;

        return $this;
    }

    /**
     * Get fifthToEighthEn
     *
     * @return string
     */
    public function getFifthToEighthEn()
    {
        return $this->fifthToEighthEn;
    }

    /**
     * Set lan
     *
     * @param boolean $lan
     *
     * @return Reward
     */
    public function setLan($lan)
    {
        $this->lan = $lan;

        return $this;
    }

    /**
     * Get lan
     *
     * @return bool
     */
    public function getLan()
    {
        return $this->lan;
    }

    /**
     * Set activated
     *
     * @param boolean $activated
     *
     * @return Reward
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
}
