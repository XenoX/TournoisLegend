<?php

namespace TournamentBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Workflow;
use TournamentBundle\Entity\LanParticipant;
use TournamentBundle\Workflow\LanWorkflow;

/**
 * Class LanParticipantListener
 * @package TournamentBundle\EventListener
 */
class LanParticipantListener implements EventSubscriber
{
    /** @var Workflow */
    private $workflow;

    /**
     * @param Workflow $workflow
     */
    public function __construct(Workflow $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::prePersist];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (false === $entity instanceof LanParticipant) {
            return;
        }

        /** @var LanParticipant $entity */
        $entity->setState(LanWorkflow::STATE_REGISTERED);

        if ($entity->getLan()->isFree()) {
            try {
                $this->workflow->apply($entity, LanWorkflow::TRANS_CONFIRM);
            } catch (LogicException $exception) {
                return;
            }
        }
    }
}
