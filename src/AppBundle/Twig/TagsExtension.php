<?php

namespace AppBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use TournamentBundle\Entity\Participant;

/**
 * Class TagsExtension
 * @package AppBundle\Twig
 */
class TagsExtension extends \Twig_Extension
{
    /** @var EntityManagerInterface */
    private $manager;

    /**
     * RolesExtension constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('tags', [$this, 'tags'])];
    }

    /**
     * @param Participant $participant
     *
     * @return string
     */
    public function tags(Participant $participant)
    {
        if ($participant->getTournament()->isSolo()) {
            return $participant->getTag();
        }

        $participants = $this->manager->getRepository(Participant::class)->findBy(
            ['tournament' => $participant->getTournament(), 'team' => $participant->getTeam()]
        );

        $tags = '';
        foreach ($participants as $participant) {
            $tags .= $participant->getTag() ? $participant->getTag().', ' : '';
        }
        $tags = substr($tags, 0, -2);

        return $tags;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tags_extension';
    }
}
