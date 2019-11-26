<?php

namespace TournamentBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Workflow;
use TournamentBundle\Entity\Participant;
use TournamentBundle\Entity\Tournament;
use TournamentBundle\Workflow\ParticipantWorkflow;
use TournamentBundle\Workflow\TournamentWorkflow;
use UserBundle\Entity\Notification;
use UserBundle\Entity\NotificationTemplate;
use UserBundle\Entity\Team;
use UserBundle\Entity\TeamMember;
use UserBundle\Entity\User;
use UserBundle\Service\NotificationService;

/**
 * Class ParticipantService
 * @package TournamentBundle\Service
 */
class ParticipantService
{
    /** @var EntityManager */
    protected $manager;

    /** @var FlashBagInterface */
    protected $flashbag;

    /** @var TokenStorageInterface */
    protected $token;

    /** @var NotificationService */
    protected $notificator;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var Workflow */
    protected $workflow;

    /**
     * ParticipantService constructor.
     *
     * @param EntityManager         $manager
     * @param FlashBagInterface     $flashBag
     * @param TokenStorageInterface $token
     * @param NotificationService   $notificator
     * @param TranslatorInterface   $translator
     * @param Workflow              $workflow
     */
    public function __construct(
        EntityManager $manager,
        FlashBagInterface $flashBag,
        TokenStorageInterface $token,
        NotificationService $notificator,
        TranslatorInterface $translator,
        Workflow $workflow
    ) {
        $this->manager = $manager;
        $this->flashbag = $flashBag;
        $this->token = $token;
        $this->notificator = $notificator;
        $this->translator = $translator;
        $this->workflow = $workflow;
    }

    /**
     * @param Tournament $tournament
     *
     * @return array|null
     */
    public function getTournamentParticipants(Tournament $tournament)
    {
        $participants = $this->manager->getRepository(Participant::class)->findBy(['tournament' => $tournament]);

        $tournamentParticipants = [];
        /** @var Participant $participant */
        foreach ($participants as $participant) {
            $teamOrUser = $participant->getTeamOrUser();

            if (!isset($tournamentParticipants[$teamOrUser->getId()])) {
                $tournamentParticipants[$teamOrUser->getId()] = [];
            }

            $tournamentParticipants[$teamOrUser->getId()][] = $participant;
        }

        return $tournamentParticipants;
    }

    /**
     * @param Tournament $tournament
     *
     * @return Participant[]
     */
    public function getTournamentParticipantsToStart(Tournament $tournament)
    {
        return $this->cleanParticipantsToStart($this->manager->getRepository(Participant::class)->findBy(['tournament' => $tournament]));
    }

    /**
     * Get one action possibility by user and tournament
     *
     * @param User|null  $user
     * @param Tournament $tournament
     *
     * @return integer
     */
    public function getPossibility(User $user = null, Tournament $tournament)
    {
        if ($tournament->isStarted() || $tournament->isDone() || null === $user) {
            return Participant::CAN_NOTHING;
        }

        $alreadyRegistered = $this->manager->getRepository(Participant::class)->findOneBy(
            ['tournament' => $tournament, 'user' => $user]
        );

        if ($alreadyRegistered) {
            if (ParticipantWorkflow::STATE_REGISTERED === $alreadyRegistered->getState()) {
                return Participant::CAN_CONFIRM;
            }

            if (false === $tournament->isCheckInPhase()) {
                return Participant::CAN_NOTHING;
            }

            if (ParticipantWorkflow::STATE_CONFIRMED === $alreadyRegistered->getState()) {
                if ($tournament->isTeam()) {
                    $participants = $this->manager->getRepository(Participant::class)->findBy(
                        ['tournament' => $tournament, 'team' => $alreadyRegistered->getTeam()]
                    );

                    foreach ($participants as $participant) {
                        if (ParticipantWorkflow::STATE_CONFIRMED !== $participant->getState()) {
                            return Participant::CAN_NOTHING;
                        }
                    }
                }

                return Participant::CAN_CHECK_IN;
            }

            return Participant::CAN_NOTHING;
        }

        if ($tournament->isRegistrationPhase() || $tournament->isCheckInPhase()) {
            return Participant::CAN_REGISTRATION;
        }

        return Participant::CAN_NOTHING;
    }

    /**
     * @param array $users
     *
     * @return array
     */
    public function getUserTags(array $users)
    {
        $userTags = [];

        /** @var User $user */
        foreach ($users as $key => $user) {
            if (false === $user->isClean()) {
                continue;
            }

            $lastParticipant = $this->manager->getRepository(Participant::class)
                ->findLastParticipantForUser($user, 1)
            ;

            $userTags[$user->getId()] = $lastParticipant ? $lastParticipant->getTag() : null;
        }

        return $userTags;
    }

    /**
     * @param string|null $tag
     *
     * @return bool
     */
    public function isCleanTag(string $tag = null)
    {
        if (null === $tag) {
            return true;
        }

        if (null !== $this->manager->getRepository(Participant::class)->getBanParticipantWithTag($tag)) {
            return false;
        }

        return true;
    }

    /**
     * @param User       $user
     * @param Team       $team
     * @param Tournament $tournament
     * @param array      $users
     *
     * @return bool
     */
    public function canAccessToPlayersPage(User $user, Team $team, Tournament $tournament, array $users)
    {
        if (
            false === $this->isUserInTeam($user, $team) ||
            true === $tournament->isSolo() || 
            false === $tournament->canRegistration() ||
            false === $this->checkMinimumParticipants($users)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param User        $user
     * @param Tournament  $tournament
     * @param Team|null   $team
     * @param string|null $tag
     *
     * @return Participant
     */
    public function create(User $user, Tournament $tournament, Team $team = null, string $tag = null)
    {
        $participantToRemove = $this->manager->getRepository(Participant::class)->findOneBy([
            'user' => $user, 'tournament' => $tournament
        ]);

        $participant = new Participant();

        if (null !== $participantToRemove) {
            $participant->setRegisterAt($participantToRemove->getRegisterAt());

            try {
                $this->manager->remove($participantToRemove);
            } catch (ORMException $e) {
                return $participant;
            }
        }

        if ($tournament->isCheckInPhase()) {
            $participant
                ->setCheckInAt(new \DateTime())
                ->setState(ParticipantWorkflow::STATE_CHECKED_IN)
            ;
        }

        $participant
            ->setUser($user)
            ->setTeam($team)
            ->setTournament($tournament)
        ;

        if (null !== $tag) {
            $participant->setTag($tag);
        }

        $currentUser = $this->token->getToken() ? $this->token->getToken()->getUser() : null;
        if (null !== $currentUser && $currentUser !== $user) {
            $this->notificator->add(
                $user,
                Notification::TYPE_TOURNAMENT,
                NotificationTemplate::TOURNAMENT_TEAM_REGISTRATION,
                $tournament->getId()
            );
        }

        try {
            $this->manager->persist($participant);
        } catch (ORMException $e) {
            return $participant;
        }

        return $participant;
    }

    /**
     * @param User       $user
     * @param Tournament $tournament
     * @param Team       $team
     *
     * @return bool
     */
    public function userAlreadyRegisteredWithAnotherTeam(User $user, Tournament $tournament, Team $team)
    {
        $isAlreadyRegisteredWithAnotherTeam = $this->manager->getRepository(Participant::class)
            ->findAlreadyRegisteredWithAnotherTeam($user, $tournament, $team)
        ;

        if (null !== $isAlreadyRegisteredWithAnotherTeam) {
            $this->flashbag->add('danger', $this->translator->trans(
                'registration.alert.danger.player_already_registered', [
                    '%username%' => $user->getUsername(),
                    '%team%' => $isAlreadyRegisteredWithAnotherTeam->getTeam()->getName()
                ]
            ));

            return true;
        }

        return false;
    }

    /**
     * @param User       $user
     * @param Tournament $tournament
     *
     * @return TeamMember[]
     */
    public function getRegisterableTeamMembers(User $user, Tournament $tournament)
    {
        return $this->removeAlreadyRegistered(
            $this->manager->getRepository(TeamMember::class)->findRegisterable($user), $tournament
        );
    }

    /**
     * @param Participant $participant
     *
     * @return bool
     */
    public function checkIn(Participant $participant)
    {
        if (false === $participant->getTeamOrUser()->isClean()) {
            $this->flashbag->add('danger', 'registration.alert.danger.ban');

            return false;
        }

        $tournament = $participant->getTournament();
        $participantsToCheckIn = [$participant];
        if (true === $tournament->isTeam()) {
            $participantsToCheckIn = $this->manager->getRepository(Participant::class)->findBy(
                ['tournament' => $tournament, 'team' => $participant->getTeam()]
            );

            if ($tournament->getFormat() > count($participantsToCheckIn)) {
                $this->flashbag->add('danger', 'registration.alert.danger.minimum_confirmed');

                return false;
            }
        }

        foreach ($participantsToCheckIn as $participantToCheckIn) {
            try {
                $this->workflow->apply($participantToCheckIn, ParticipantWorkflow::TRANS_CHECK_IN);
            } catch (LogicException $exception) {
                $this->flashbag->add('danger', $exception->getMessage());

                return false;
            }
        }

        return true;
    }

    /**
     * @param User       $user
     * @param Tournament $tournament
     */
    public function remove(User $user, Tournament $tournament)
    {
        $participant = $this->manager->getRepository(Participant::class)->findOneBy(['tournament' => $tournament, 'user' => $user]);

        if ($participant) {
            try {
                $this->manager->remove($participant);
            } catch (ORMException $e) {
                return;
            }
        }
    }

    /**
     * @param Participant $participant
     */
    public function removeParticipants(Participant $participant)
    {
        $participantsToRemove = [$participant];
        $tournament = $participant->getTournament();

        if (true === $tournament->isTeam()) {
            $participantsToRemove = $this->manager->getRepository(Participant::class)->findBy(
                ['tournament' => $tournament, 'team' => $participant->getTeam()]
            );
        }

        foreach ($participantsToRemove as $participant) {
            try {
                $this->manager->remove($participant);
            } catch (ORMException $e) {
                return;
            }
        }
    }

    /**
     * @param array $users
     *
     * @return bool
     */
    private function checkMinimumParticipants(array $users)
    {
        if (Participant::MINIMUM_TEAM_PARTICIPANT > count($users)) {
            $this->flashbag->add('danger', $this->translator->trans(
                'registration.alert.danger.minimum_participant',
                ['%minimum%' => Participant::MINIMUM_TEAM_PARTICIPANT]
            ));

            return false;
        }

        return true;
    }

    /**
     * @param TeamMember[] $teamsMember
     * @param Tournament   $tournament
     *
     * @return TeamMember[]
     */
    private function removeAlreadyRegistered(array $teamsMember, Tournament $tournament)
    {
        /** @var TeamMember $teamMember */
        foreach ($teamsMember as $key => $teamMember) {
            $alreadyRegistered = $this->manager->getRepository(Participant::class)->findOneBy(['tournament' => $tournament, 'team' => $teamMember->getTeam()]);
            if ($alreadyRegistered) {
                unset($teamsMember[$key]);
            }
        }

        return $teamsMember;
    }

    /**
     * @param User $user
     * @param Team $team
     *
     * @return bool
     */
    private function isUserInTeam(User $user, Team $team)
    {
        return $this->manager->getRepository(TeamMember::class)->findOneBy(['user' => $user, 'team' => $team]) ? true : false;
    }

    /**
     * @param Participant[] $participantsToClean
     *
     * @return Participant[]
     */
    private function cleanParticipantsToStart(array $participantsToClean)
    {
        $participants = [];
        /** @var Participant $participant */
        foreach ($participantsToClean as $participant) {
            if (ParticipantWorkflow::STATE_REGISTERED === $participant->getState()) {
                continue;
            }

            $teamOrUser = $participant->getTeamOrUser();

            if (false === $teamOrUser->isClean()) {
                continue;
            }

            if (!isset($participants[$teamOrUser->getId()])) {
                $participants[$teamOrUser->getId()] = [];
            }

            $participants[$teamOrUser->getId()][] = $participant;
        }

        return $participants;
    }
}
