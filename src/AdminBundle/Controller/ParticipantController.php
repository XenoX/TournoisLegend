<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Tag;
use Doctrine\Common\Util\ClassUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TournamentBundle\Entity\Battle;
use TournamentBundle\Entity\Participant;
use TournamentBundle\Entity\Tournament;
use TournamentBundle\Form\PlayersType;
use UserBundle\Entity\Team;
use UserBundle\Entity\TeamMember;
use UserBundle\Entity\User;

/**
 * @Route("/tournament/participant")
 */
class ParticipantController extends Controller
{
    /**
     * @Route("/check-in/{participant}")
     */
    public function checkIn(Participant $participant)
    {
        $this->get('tournament.participant')->checkIn($participant);

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToManageTournament($participant->getTournament());
    }

    /**
     * @Route("/remove/{participant}")
     */
    public function remove(Participant $participant)
    {
        $this->get('tournament.participant')->removeParticipants($participant);

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToManageTournament($participant->getTournament());
    }

    /**
     * @Route("/change/{tournament}/{team}")
     * @Template()
     */
    public function change(Request $request, Tournament $tournament, Team $team)
    {
        if ($tournament->isSolo()) {
            return $this->redirectToManageTournament($tournament);
        }

        $em = $this->getDoctrine()->getManager();
        $participantService = $this->get('tournament.participant');

        $users = [];
        /** @var TeamMember $tm */
        foreach ($em->getRepository(TeamMember::class)->findBy(['team' => $team, 'requested' => false]) as $tm) {
            $users[] = $tm->getUser();
        }

        $tag = $em->getRepository(Tag::class)->findOneBy(['game' => $tournament->getMode()->getGame()]);
        $form = $this->createForm(PlayersType::class, [
            'users' => $users, 'tags' => $participantService->getUserTags($users), 'tag' => $tag, 'locale' => $request->getLocale()
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->getData() as $users) {
                if (is_array($users)) {
                    /** @var User $user */
                    foreach ($users as $user) {
                        if (false === is_object($user) || User::class !== ClassUtils::getRealClass(get_class($user))) {
                            continue;
                        }

                        $participantAlreadyExist = $em->getRepository(Participant::class)->findOneBy([
                            'user' => $user, 'tournament' => $tournament
                        ]);

                        if (false === $form->get('user_' . $user->getId())->getData()) {
                            // If participant to remove is present in battle, take second participant and change id
                            if ($participantAlreadyExist) {
                                $battles1 = $em->getRepository(Battle::class)->findBy(['tournament' => $tournament, 'participant1' => $participantAlreadyExist]);
                                $battles2 = $em->getRepository(Battle::class)->findBy(['tournament' => $tournament, 'participant2' => $participantAlreadyExist]);
                                if (0 < \count($battles1) || 0 < \count($battles2)) {
                                    $otherParticipant = $em->getRepository(Participant::class)->findBy(['tournament' => $tournament, 'team' => $team], null, 1, 1);

                                    if (array_key_exists(0, $otherParticipant)) {
                                        foreach ($battles1 as $battle1) {
                                            $battle1->setParticipant1($otherParticipant[0]);
                                        }
                                        foreach ($battles2 as $battle2) {
                                            $battle2->setParticipant2($otherParticipant[0]);
                                        }
                                    }

                                    $em->flush();
                                }
                            }

                            $participantService->remove($user, $tournament);
                            continue;
                        }

                        if (true === $participantService->userAlreadyRegisteredWithAnotherTeam($user, $tournament, $team)) {
                            return $this->redirectToManageTournament($tournament);
                        }

                        if ($participantAlreadyExist) {
                            $participantAlreadyExist->setTag($form->get('tag_' . $user->getId())->getData());
                            continue;
                        }

                        $participantService->create(
                            $user,
                            $tournament,
                            $team,
                            $form->get('tag_' . $user->getId())->getData()
                        );
                    }
                }
            }

            $this->addFlash('success', 'Line up updated with success!');
            $em->flush();

            return $this->redirectToManageTournament($tournament);
        }

        $participants = $em->getRepository(Participant::class)->findBy(['tournament' => $tournament, 'team' => $team]);
        $registeredUsers = [];
        foreach ($participants as $participant) {
            $registeredUsers[$participant->getUser()->getId()] = $participant->getUser();
        }

        return [
            'tournament' => $tournament,
            'team' => $team,
            'users' => $users,
            'registeredUsers' => $registeredUsers,
            'form' => $form->createView(),
            'minParticipant' => Participant::MINIMUM_TEAM_PARTICIPANT
        ];
    }

    /**
     * @param Tournament $tournament
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function redirectToManageTournament(Tournament $tournament)
    {
        return $this->redirectToRoute('admin_tournament_manage', ['tournament' => $tournament->getId()]);
    }
}
