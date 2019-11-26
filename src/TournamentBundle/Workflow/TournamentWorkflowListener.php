<?php

namespace TournamentBundle\Workflow;

use TournamentBundle\Service\RankingService;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class TournamentWorkflowListener
 * @package TournamentBundle\Workflow
 */
class TournamentWorkflowListener implements EventSubscriberInterface
{
    /** @var RankingService */
    private $rankingService;

    /**
     * TournamentWorkflowListener constructor.
     *
     * @param RankingService $rankingService
     */
    public function __construct(RankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    /**
     * @param Event $event
     */
    public function match(Event $event)
    {
        /** @var \TournamentBundle\Entity\Tournament $entity */
        $entity = $event->getSubject();

        $entity->setStarted(true);
    }

    /**
     * @param Event $event
     */
    public function done(Event $event)
    {
        /** @var \TournamentBundle\Entity\Tournament $entity */
        $entity = $event->getSubject();

        $this->rankingService->update($entity);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.tournament.transition.'.TournamentWorkflow::TRANS_MATCH => ['match'],
            'workflow.tournament.transition.'.TournamentWorkflow::TRANS_DONE => ['done'],
        ];
    }
}
