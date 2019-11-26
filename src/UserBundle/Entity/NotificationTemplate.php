<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NotificationTemplate
 *
 * @ORM\Table(name="notification_template")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\NotificationTemplateRepository")
 * @UniqueEntity("name")
 */
class NotificationTemplate
{
    const WELCOME = 'welcome';
    const ADMIN_TEAM_MEMBER_LEADER = 'admin.team.member.leader';
    const TEAM_MEMBER_ACCEPTED = 'team.member.accepted';
    const TEAM_MEMBER_JOINED = 'team.member.joined';
    const TEAM_MEMBER_LEADER = 'team.member.leader';
    const TEAM_MEMBER_REQUESTED = 'team.member.requested';
    const TEAM_MEMBER_ROLE = 'team.member.role';
    const TEAM_DELETED = 'team.deleted';
    const TOURNAMENT_TEAM_REGISTRATION = 'tournament.team.registration';
    const TOURNAMENT_SOLO_SELECTED = 'tournament.solo.selected';
    const TOURNAMENT_SOLO_ACCEPTED = 'tournament.solo.accepted';
    const TOURNAMENT_STREAM_ACCEPTED = 'tournament.stream.accepted';

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
     * @ORM\Column(name="content_en", type="string", length=255)
     */
    private $contentEn;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotNull()
     *
     * @ORM\Column(name="content_fr", type="string", length=255)
     */
    private $contentFr;


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
     * @return NotificationTemplate
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
     * Set contentEn
     *
     * @param string $contentEn
     *
     * @return NotificationTemplate
     */
    public function setContentEn($contentEn)
    {
        $this->contentEn = $contentEn;

        return $this;
    }

    /**
     * Get contentEn
     *
     * @return string
     */
    public function getContentEn()
    {
        return $this->contentEn;
    }

    /**
     * Set contentFr
     *
     * @param string $contentFr
     *
     * @return NotificationTemplate
     */
    public function setContentFr($contentFr)
    {
        $this->contentFr = $contentFr;

        return $this;
    }

    /**
     * Get contentFr
     *
     * @return string
     */
    public function getContentFr()
    {
        return $this->contentFr;
    }
}
