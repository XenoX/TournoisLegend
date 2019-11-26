<?php

namespace TournamentBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Workflow;
use TournamentBundle\Entity\Stream;
use TournamentBundle\Workflow\StreamWorkflow;
use UserBundle\Entity\User;

/**
 * Class StreamListener
 * @package TournamentBundle\EventListener
 */
class StreamListener implements EventSubscriber
{
    /** @var Workflow */
    private $workflow;

    /** @var AuthorizationCheckerInterface */
    private $security;

    /**
     * @param Workflow                      $workflow
     * @param AuthorizationCheckerInterface $security
     */
    public function __construct(Workflow $workflow, AuthorizationCheckerInterface $security)
    {
        $this->workflow = $workflow;
        $this->security = $security;
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

        if (false === $entity instanceof Stream) {
            return;
        }

        /** @var Stream $entity */
        $entity->setState(StreamWorkflow::STATE_REQUESTED);

        if ($this->security->isGranted(User::ROLE_STREAMER, $entity->getUser())) {
            try {
                $this->workflow->apply($entity, StreamWorkflow::TRANS_ACCEPT);
            } catch (LogicException $exception) {
                return;
            }
        }
    }
}
