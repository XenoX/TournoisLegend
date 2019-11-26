<?php

namespace TournamentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TournamentBundle\Entity\Tournament;

/**
 * @Route("/stream")
 */
class StreamController extends Controller
{
    /**
     * @Route("/request/{tournament}")
     * @Method("POST")
     */
    public function requestAction(Request $request, Tournament $tournament)
    {
        if (false === $tournament->getStreams()) {
            return $this->redirectToTournament($tournament);
        }

        $data = $request->get('stream_request');

        if (false === $this->get('tournament.stream')->add($data['channel'], $tournament, $this->getUser())) {
            $this->addFlash('danger', 'stream.alert.danger.not_valid_or_already_registered');

            return $this->redirectToTournament($tournament);
        }

        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'stream.alert.success.request');

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
