<?php

namespace TournamentBundle\Workflow;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TournamentBundle\Entity\Battle;
use TournamentBundle\Entity\Participant;
use TournamentBundle\Entity\Tournament;
use TournamentBundle\Service\TournamentService;

/**
 * Class GuardSubscriber
 * @package TournamentBundle\Workflow
 */
class GuardSubscriber implements EventSubscriberInterface
{
    /** @var EntityManager */
    private $manager;

    /** @var TournamentService */
    private $tournamentService;

    /**
     * GuardSubscriber constructor.
     *
     * @param EntityManager     $manager
     * @param TournamentService $tournamentService
     */
    public function __construct(EntityManager $manager, TournamentService $tournamentService)
    {
        $this->manager = $manager;
        $this->tournamentService = $tournamentService;
    }

    /**
     * @param GuardEvent $event
     */
    public function checkTagForConfirm(GuardEvent $event)
    {
        /** @var Participant $participant */
        $participant = $event->getSubject();

        if (empty($participant->getTag())) {
            $this->tournamentService->sendConfirmMail($participant);

            $event->setBlocked(true);
        }
    }

    /**
     * @param GuardEvent $event
     */
    public function checkPhaseForCheckIn(GuardEvent $event)
    {
        /** @var Participant $participant */
        $participant = $event->getSubject();

        if (false === $participant->getTournament()->isCheckInPhase()) {
            $event->setBlocked(true);
        }
    }

    /**
     * @param GuardEvent $event
     */
    public function checkCanStop(GuardEvent $event)
    {
        /** @var Tournament $tournament */
        $tournament = $event->getSubject();

        $endedBattles = $this->manager->getRepository(Battle::class)->findEndedBattlesForTournament($tournament);

        if ($tournament->getSize() !== count($endedBattles)) {
            $event->setBlocked(true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.participant.guard.'.ParticipantWorkflow::TRANS_CONFIRM => ['checkTagForConfirm'],
            'workflow.participant.guard.'.ParticipantWorkflow::TRANS_CHECK_IN => ['checkPhaseForCheckIn'],
            'workflow.tournament.guard.'.TournamentWorkflow::TRANS_DONE => ['checkCanStop'],
        ];
    }
}
