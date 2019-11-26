<?php

namespace TournamentBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Workflow;
use TournamentBundle\Entity\Participant;
use TournamentBundle\Workflow\ParticipantWorkflow;

/**
 * Class ParticipantListener
 * @package TournamentBundle\EventListener
 */
class ParticipantListener implements EventSubscriber
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
        return [Events::prePersist, Events::preUpdate];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (false === $entity instanceof Participant) {
            return;
        }

        /** @var Participant $entity */
        $entity->setState(ParticipantWorkflow::STATE_REGISTERED);

        $this->confirm($entity);
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (false === $entity instanceof Participant) {
            return;
        }

        /** @var Participant $entity */
        $this->confirm($entity);
    }

    /**
     * @param Participant $participant
     */
    private function confirm(Participant $participant)
    {
        try {
            $this->workflow->apply($participant, ParticipantWorkflow::TRANS_CONFIRM);

            if ($participant->getTournament()->isCheckInPhase()) {
                $this->workflow->apply($participant, ParticipantWorkflow::TRANS_CHECK_IN);
            }
        } catch (LogicException $exception) {
            return;
        }
    }
}
