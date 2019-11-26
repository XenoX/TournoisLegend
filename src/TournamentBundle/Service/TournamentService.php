<?php

namespace TournamentBundle\Service;

use AppBundle\Service\MailService;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Workflow;
use TournamentBundle\Entity\Participant;
use TournamentBundle\Entity\Tournament;
use TournamentBundle\Workflow\TournamentWorkflow;

/**
 * Class TournamentService
 * @package TournamentBundle\Service
 */
class TournamentService
{
    /** @var EntityManager */
    private $manager;

    /** @var FlashBagInterface */
    private $flashbag;

    /** @var TranslatorInterface */
    private $translator;

    /** @var Workflow */
    private $workflow;

    /** @var MailService */
    private $mail;

    /** @var RouterInterface */
    private $router;

    /** @var SlugifyInterface */
    private $slugify;

    /**
     * TournamentService constructor.
     *
     * @param EntityManager       $manager
     * @param FlashBagInterface   $flashBag
     * @param TranslatorInterface $translator
     * @param Workflow            $workflow
     * @param MailService         $mail
     * @param RouterInterface     $router
     * @param SlugifyInterface    $slugify
     */
    public function __construct(
        EntityManager $manager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        Workflow $workflow,
        MailService $mail,
        RouterInterface $router,
        SlugifyInterface $slugify
    ) {
        $this->manager = $manager;
        $this->flashbag = $flashBag;
        $this->translator = $translator;
        $this->workflow = $workflow;
        $this->mail = $mail;
        $this->router = $router;
        $this->slugify = $slugify;
    }

    /**
     * @param Tournament $tournament
     *
     * @return bool
     */
    public function changeStateIfNeeded(Tournament $tournament)
    {
        $state = $tournament->getState();

        if (false === in_array($state, TournamentWorkflow::STATES_BEFORE_CHECKIN)) {
            return false;
        }

        if (TournamentWorkflow::STATE_REGISTRATION !== $state && $tournament->isRegistrationDatetimePhase()) {
            try {
                $this->workflow->apply($tournament, TournamentWorkflow::TRANS_REGISTRATION);
            } catch (LogicException $exception) {
                return false;
            }
        }

        if (TournamentWorkflow::STATE_CHECK_IN !== $state && $tournament->isCheckInDatetimePhase()) {
            try {
                $this->workflow->apply($tournament, TournamentWorkflow::TRANS_CHECK_IN);
            } catch (LogicException $exception) {
                return false;
            }

            $this->sendCheckInMail($tournament);
        }

        return true;
    }

    /**
     * @param Tournament $tournament
     * @param array      $participants
     *
     * @return bool
     */
    public function canStart(Tournament $tournament, array $participants)
    {
        if ($tournament->isStarted()) {
            $this->flashbag->add('danger', 'Tournament already started');

            return false;
        }

        if (count($participants) < Tournament::MINIMUM_PARTICIPANT) {
            $this->flashbag->add('danger', Tournament::MINIMUM_PARTICIPANT.' minimum participants');

            return false;
        }

        if (count($participants) > $tournament->getSize()) {
            $this->flashbag->add('danger', 'Too many participant for this tournament ('.count($participants).' for '.$tournament->getSize().' slots)');

            return false;
        }

        return true;
    }

    /**
     * @param Tournament $tournament
     * @param int        $numberOfParticipants
     */
    public function reduceSizeIfNeeded(Tournament $tournament, int $numberOfParticipants)
    {
        while ($numberOfParticipants <= ($tournament->getSize() - $numberOfParticipants)) {
            $tournament->setSize($tournament->getSize() / 2);
        }
    }

    /**
     * @param Tournament $tournament
     *
     * @return bool
     */
    public function stop(Tournament $tournament)
    {
        try {
            $this->workflow->apply($tournament, TournamentWorkflow::TRANS_DONE);
        } catch (LogicException $exception) {
            $this->flashbag->add('danger', 'Impossible, tournament already stopped or there is a match not finished');

            return false;
        }

        return true;
    }

    /**
     * @param Participant $participant
     */
    public function sendConfirmMail(Participant $participant)
    {
        $translator = $this->translator;
        $tournament = $participant->getTournament();
        $name = $tournament->getName();

        $link = $this->router->generate('tournament_tournament_profile', [
            'game' => $this->slugify->slugify($tournament->getMode()->getGame()->getName()),
            'tournament' => $tournament->getId(),
            'slug' => $this->slugify->slugify($name)
        ]);

        if (null === $user = $participant->getUser()) {
            return;
        }

        $subject = $translator->trans('mail.subject.confirm', ['%name%' => $name], null, $user->getLocale());
        $body = $translator->trans(
            'mail.body.confirm',
            ['%username%' => $user->getName(), '%name%' => $name, '%link%' => $link],
            null,
            $user->getLocale()
        );

        $this->mail->send(
            null,
            [['email' => $user->getEmail(), 'name' => $user->getName()]],
            $subject,
            $body
        );
    }

    /**
     * @param Tournament $tournament
     */
    private function sendCheckInMail(Tournament $tournament)
    {
        $translator = $this->translator;
        $name = $tournament->getName();
        $link = $this->router->generate('tournament_tournament_profile', [
            'game' => $this->slugify->slugify($tournament->getMode()->getGame()->getName()),
            'tournament' => $tournament->getId(),
            'slug' => $this->slugify->slugify($name)
        ]);

        $participants = $this->manager->getRepository(Participant::class)->findBy(['tournament' => $tournament, 'checkInAt' => null]);
        foreach ($participants as $participant) {
            if (null === $user = $participant->getUser()) {
                continue;
            }

            $subject = $translator->trans('mail.subject.check_in', ['%name%' => $name], null, $user->getLocale());
            $body = $translator->trans(
                'mail.body.check_in',
                ['%username%' => $user->getName(), '%name%' => $name, '%link%' => $link],
                null,
                $user->getLocale()
            );

            $this->mail->send(
                null,
                [['email' => $user->getEmail(), 'name' => $user->getName()]],
                $subject,
                $body
            );
        }
    }
}
