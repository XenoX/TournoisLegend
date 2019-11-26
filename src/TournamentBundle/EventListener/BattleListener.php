<?php

namespace TournamentBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use TournamentBundle\Entity\Battle;
use TournamentBundle\Service\BattleService;

/**
 * Class BattleListener
 * @package TournamentBundle\EventListener
 */
class BattleListener implements EventSubscriber
{
    /** @var BattleService */
    private $battleService;

    /**
     * BattleListener constructor.
     *
     * @param BattleService $battleService
     */
    public function __construct(BattleService $battleService)
    {
        $this->battleService = $battleService;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::prePersist];
    }

    /**
     * Trigger win only for battle with 'BYE'
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (false === $entity instanceof Battle) {
            return;
        }

        /** @var Battle $entity */
        if (1 !== $entity->getRound()) {
            return;
        }

        if (null !== $entity->getParticipant1() && null !== $entity->getParticipant2()) {
            return;
        }

        $this->battleService->win($entity, $entity->getParticipant1() ?? $entity->getParticipant2());
    }
}
