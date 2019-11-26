<?php

namespace AdminBundle\Controller;

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
     * @Route("/win/{battle}/{winner}")
     */
    public function winAction(Battle $battle, Participant $winner)
    {
        $this->get('tournament.battle')->win($battle, $winner);

        $this->get('admin.history')->add(
            $battle->getTournament()->getId(),
            History::TOURNAMENT,
            '[Admin] Set <b>'.$winner->getTeamOrUser()->getName().'</b> winner for match '.$battle->getBattleId()
            .' (<b>'.$battle->getParticipant1()->getTeamOrUser()->getName().'</b> vs <b>'
            .$battle->getParticipant2()->getTeamOrUser()->getName().'</b>)',
            $this->getUser()
        );

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToManageTournament($battle->getTournament());
    }

    /**
     * @Route("/reset/{battle}")
     */
    public function resetAction(Battle $battle)
    {
        $this->get('tournament.battle')->reset($battle);

        $this->get('admin.history')->add(
            $battle->getTournament()->getId(),
            History::TOURNAMENT,
            '[Admin] Reset battle <b>'.$battle->getBattleId().'</b> between (<b>'
            .$battle->getParticipant1()->getTeamOrUser()->getName().'</b> and <b>'
            .$battle->getParticipant2()->getTeamOrUser()->getName().'</b>)',
            $this->getUser()
        );

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToManageTournament($battle->getTournament());
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
