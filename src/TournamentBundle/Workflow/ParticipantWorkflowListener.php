<?php

namespace TournamentBundle\Workflow;

use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ParticipantWorkflowListener
 * @package TournamentBundle\Workflow
 */
class ParticipantWorkflowListener implements EventSubscriberInterface
{
    /**
     * @param Event $event
     */
    public function confirm(Event $event)
    {
        /** @var \TournamentBundle\Entity\Participant $entity */
        $entity = $event->getSubject();

        $entity->setConfirmedAt(new \DateTime());
    }

    /**
     * @param Event $event
     */
    public function checkIn(Event $event)
    {
        /** @var \TournamentBundle\Entity\Participant $entity */
        $entity = $event->getSubject();

        $entity->setCheckInAt(new \DateTime());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.participant.transition.'.ParticipantWorkflow::TRANS_CONFIRM => ['confirm'],
            'workflow.participant.transition.'.ParticipantWorkflow::TRANS_CHECK_IN => ['checkIn'],
        ];
    }
}
