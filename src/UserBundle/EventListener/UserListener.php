<?php

namespace UserBundle\EventListener;

use AppBundle\Service\MailService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Translation\TranslatorInterface;
use UserBundle\Entity\User;

/**
 * Class UserListener
 * @package UserBundle\EventListener
 */
class UserListener implements EventSubscriber
{
    /** @var MailService */
    private $mailService;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * @param MailService         $mailService
     * @param TranslatorInterface $translator
     */
    public function __construct(MailService $mailService, TranslatorInterface $translator)
    {
        $this->mailService = $mailService;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::preUpdate];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (false === $entity instanceof User) {
            return;
        }

        $changes = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity);
        if (false === isset($changes['locked']) || false === $changes['locked'][1]) {
            return;
        }

        /** @var User $entity */
        $this->mailService->send(
            null,
            [['name' => $entity->getUsername(), 'email' => $entity->getEmail()]],
            $this->translator->trans('mail.subject.banned'),
            $this->translator->trans('mail.body.banned')
        );
    }
}
