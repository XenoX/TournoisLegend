<?php

namespace TournamentBundle\Controller;

use AdminBundle\Entity\History;
use Doctrine\Common\Util\ClassUtils;
use AppBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TournamentBundle\Entity\Participant;
use TournamentBundle\Entity\Tournament;
use TournamentBundle\Form\PlayersType;
use TournamentBundle\Form\ConfirmType;
use UserBundle\Entity\Team;
use UserBundle\Entity\TeamMember;
use UserBundle\Entity\User;

/**
 * @Route("/registration")
 */
class RegistrationController extends Controller
{
    /**
     * @Route("/{tournament}-{slug}")
     * @Template()
     */
    public function indexAction(Tournament $tournament)
    {
        $user = $this->getUser();
        $participantService = $this->get('tournament.participant');

        if (
            Participant::CAN_REGISTRATION !== $participantService->getPossibility($user, $tournament) ||
            true === $tournament->isSolo()
        ) {
            return $this->redirectToTournament($tournament);
        }

        $teamsMember = $participantService->getRegisterableTeamMembers($user, $tournament);

        return ['tournament' => $tournament, 'teamsMemberCount' => count($teamsMember)];
    }

    /**
     * @Route("/team/{tournament}-{slug}")
     * @Template()
     */
    public function teamAction(Tournament $tournament)
    {
        $user = $this->getUser();
        $participantService = $this->get('tournament.participant');

        if (
            Participant::CAN_REGISTRATION !== $participantService->getPossibility($user, $tournament) ||
            true === $tournament->isSolo()
        ) {
            return $this->redirectToTournament($tournament);
        }

        $teamsMember = $participantService->getRegisterableTeamMembers($user, $tournament);

        if (0 === count($teamsMember)) {
            return $this->redirectToTournament($tournament);
        }

        return ['tournament' => $tournament, 'teamsMember' => $teamsMember];
    }

    /**
     * @Route("/team/select-players/{tournament}-{slug}/{team}-{teamSlug}")
     * @Template()
     */
    public function playersAction(Request $request, Tournament $tournament, Team $team)
    {
        $em = $this->getDoctrine()->getManager();
        $participantService = $this->get('tournament.participant');

        $users = [];
        /** @var TeamMember $tm */
        foreach ($em->getRepository(TeamMember::class)->findBy(['team' => $team, 'requested' => false]) as $tm) {
            $users[] = $tm->getUser();
        }

        if (false === $participantService->canAccessToPlayersPage($this->getUser(), $team, $tournament, $users)) {
            return $this->redirectToTournament($tournament);
        }

        $tag = $em->getRepository(Tag::class)->findOneBy(['game' => $tournament->getMode()->getGame()]);
        $userTags = $participantService->getUserTags($users);
        $form = $this->createForm(PlayersType::class, [
            'users' => $users, 'tags' => $userTags, 'tag' => $tag, 'locale' => $request->getLocale()
        ]);

        $minParticipants = Participant::MINIMUM_TEAM_PARTICIPANT;
        if ($tournament->isCheckInPhase()) {
            $minParticipants = $tournament->getFormat();
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $countParticipant = 0;
            foreach ($form->getData() as $users) {
                if (is_array($users)) {
                    /** @var User $user */
                    foreach ($users as $user) {
                        if (false === is_object($user) || User::class !== ClassUtils::getRealClass(get_class($user))) {
                            continue;
                        }

                        if (false === $form->get('user_' . $user->getId())->getData()) {
                            $participantService->remove($user, $tournament);
                            continue;
                        }

                        if (true === $participantService->userAlreadyRegisteredWithAnotherTeam($user, $tournament, $team)) {
                            return $this->redirectToTournament($tournament);
                        }

                        $tag = $form->get('tag_' . $user->getId())->getData();
                        if (false === $participantService->isCleanTag($tag)) {
                            $this->addFlash('danger', 'registration.alert.danger.tag_banned');

                            return $this->redirectToTournament($tournament);
                        }

                        if ($tournament->isCheckInPhase() && null === $tag) {
                            $this->addFlash('danger', 'registration.alert.danger.all_tag_needed');
                            $slugify = $this->get('slugify');

                            return $this->redirectToRoute('tournament_registration_players', [
                                'tournament' => $tournament->getId(),
                                'slug' => $slugify->slugify($tournament->getName()),
                                'team' => $team->getId(),
                                'teamSlug' => $slugify->slugify($team->getName())
                            ]);
                        }

                        $participantService->create(
                            $user,
                            $tournament,
                            $team,
                            $tag
                        );

                        ++$countParticipant;
                    }
                }
            }

            if ($minParticipants <= $countParticipant) {
                $this->addFlash('success', 'registration.alert.success.registered');

                $this->get('admin.history')->add(
                    $tournament->getId(),
                    History::TOURNAMENT,
                    'Team <b>'.$team->getName().'</b> registered',
                    $this->getUser()
                );

                $em->flush();

                return $this->redirectToTournament($tournament);
            }

            $this->addFlash('danger', $this->get('translator')->trans(
                'registration.alert.danger.minimum_participant',
                ['%minimum%' => $minParticipants]
            ));
        }

        $participants = $em->getRepository(Participant::class)->findBy(['tournament' => $tournament, 'team' => $team]);
        $registeredUsers = [];
        /** @var Participant $participant */
        foreach ($participants as $participant) {
            $registeredUsers[$participant->getUser()->getId()] = $participant->getUser();
        }

        return [
            'tournament' => $tournament,
            'team' => $team,
            'users' => $users,
            'registeredUsers' => $registeredUsers,
            'form' => $form->createView(),
            'minParticipant' => $minParticipants
        ];
    }

    /**
     * @Route("/confirm/{tournament}-{slug}")
     * @Template()
     */
    public function confirmAction(Request $request, Tournament $tournament)
    {
        $user = $this->getUser();
        $participantService = $this->get('tournament.participant');
        $possibility = $participantService->getPossibility($user, $tournament);
        if (Participant::CAN_REGISTRATION !== $possibility && Participant::CAN_CONFIRM !== $possibility) {
            return $this->redirectToTournament($tournament);
        }

        $em = $this->getDoctrine()->getManager();
        $participant = $em->getRepository(Participant::class)->findOneBy(['tournament' => $tournament, 'user' => $user]);

        if ($participant && true === $tournament->isSolo()) {
            return $this->redirectToTournament($tournament);
        }

        $game = $tournament->getMode()->getGame();
        $form = $this->createForm(ConfirmType::class, [
            'tag' => $em->getRepository(Tag::class)->findOneBy(['game' => $game]),
            'locale' => $this->getParameter('locale')
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tag = $form->get('value')->getData();
            if (3 > strlen($tag) || empty($tag)) {
                $this->addFlash('danger', 'registration.alert.danger.tag_length_minimum');

                return $this->redirectToTournament($tournament);
            }

            if (false === $participantService->isCleanTag($tag)) {
                $this->addFlash('danger', 'registration.alert.danger.tag_banned_solo');

                return $this->redirectToTournament($tournament);
            }

            $flash = 'registration.alert.success.confirmed';
            if (null === $participant) {
                $flash = 'registration.alert.success.registered';
            }

            $participantService->create($user, $tournament, $participant ? $participant->getTeam() : null, $tag);

            $em->flush();

            $this->addFlash('success', $flash);

            return $this->redirectToTournament($tournament);
        }

        $userTags = $em->getRepository(Participant::class)->findLastParticipantForUser($user, 5);

        return ['tournament' => $tournament, 'userTags' => $userTags, 'form' => $form->createView()];
    }

    /**
     * @Route("/check-in/{tournament}-{slug}")
     */
    public function checkAction(Tournament $tournament)
    {
        $user = $this->getUser();
        $participantService = $this->get('tournament.participant');
        if (Participant::CAN_CHECK_IN !== $participantService->getPossibility($user, $tournament)) {
            return $this->redirectToTournament($tournament);
        }

        $em = $this->getDoctrine()->getManager();
        $participant = $em->getRepository(Participant::class)->findOneBy(['tournament' => $tournament, 'user' => $user]);

        if (false === $participantService->checkIn($participant)) {
            return $this->redirectToTournament($tournament);
        }

        $this->get('admin.history')->add(
            $tournament->getId(),
            History::TOURNAMENT,
            'Check in for <b>'.$participant->getTeamOrUser()->getName().'</b>',
            $user
        );

        $em->flush();
        $this->addFlash('success', 'registration.alert.success.check_in');

        return $this->redirectToTournament($tournament);
    }

    /**
     * @Route("/delete/{tournament}-{slug}")
     */
    public function deleteAction(Tournament $tournament)
    {
        if (true === $tournament->isStarted()) {
            return $this->redirectToRoute($tournament);
        }

        $em = $this->getDoctrine()->getManager();

        $participant = $em->getRepository(Participant::class)->findOneBy(
            ['tournament' => $tournament, 'user' => $this->getUser()]
        );

        $this->get('tournament.participant')->removeParticipants($participant);

        $this->get('admin.history')->add(
            $tournament->getId(),
            History::TOURNAMENT,
            'Remove participation for <b>'.$participant->getTeamOrUser()->getName().'</b>',
            $this->getUser()
        );

        $em->flush();
        $this->addFlash('success', 'registration.alert.success.removed');

        return $this->redirectToTournament($tournament);
    }

    /**
     * @param Tournament $tournament
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function redirectToTournament(Tournament $tournament)
    {
        $slugify = $this->get('slugify');

        return $this->redirectToRoute('tournament_tournament_profile', [
            'game' => $slugify->slugify($tournament->getMode()->getGame()->getName()),
            'tournament' => $tournament->getId(),
            'slug' => $slugify->slugify($tournament->getName())
        ]);
    }
}
