<?php

namespace TournamentBundle\Controller;

use AdminBundle\Entity\History;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TournamentBundle\Entity\Battle;
use TournamentBundle\Entity\Participant;
use TournamentBundle\Entity\Tournament;

/**
 * @Route("/battle")
 */
class BattleController extends Controller
{
    /**
     * @Route("/win/{battle}")
     */
    public function winAction(Battle $battle)
    {
        $tournament = $battle->getTournament();
        if (false === $battle->isReady() || true === $battle->isDone()) {
            return $this->redirectToTournament($tournament);
        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        /** @var Participant $participant */
        $participant = $em->getRepository(Participant::class)->findOneBy(
            ['tournament' => $tournament, 'user' => $user]
        );

        if (null === $participant) {
            return $this->redirectToTournament($tournament);
        }

        if ($tournament->isTeam()) {
            $team = $participant->getTeam();
            if ($team->getId() === $battle->getParticipant1()->getTeam()->getId()) {
                $participant = $battle->getParticipant1();
            } else {
                $participant = $battle->getParticipant2();
            }
        }

        $this->get('tournament.battle')->win($battle, $participant);

        $this->get('admin.history')->add(
            $tournament->getId(),
            History::TOURNAMENT,
            'Set <b>'.$participant->getTeamOrUser()->getName().'</b> winner for match '.$battle->getBattleId()
            .' (<b>'.$battle->getParticipant1()->getTeamOrUser()->getName().'</b> vs <b>'
            .$battle->getParticipant2()->getTeamOrUser()->getName().'</b>)',
            $this->getUser()
        );

        $em->flush();

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
