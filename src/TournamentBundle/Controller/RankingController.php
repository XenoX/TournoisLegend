<?php

namespace TournamentBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Entity\Mode;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TournamentBundle\Entity\Ranking;
use TournamentBundle\Entity\RankingLevel;
use TournamentBundle\Entity\RankingParticipant;

/**
 * @Route("/ranking")
 */
class RankingController extends Controller
{
    /**
     * @Route("/{slugGame}/{ranking}-{rankingSlug}")
     * @Template()
     */
    public function rankingAction(Request $request, Ranking $ranking)
    {
        $em = $this->getDoctrine()->getManager();
        $rankingParticipants = $em->getRepository(RankingParticipant::class)->findBy(['ranking' => $ranking], ['elo' => 'DESC']);
        $mode = $ranking->getMode();
        $game = $mode->getGame();

        if (null === $rankingParticipants) {
            return $this->redirectToRoute('tournament_ranking_game', [
                'slug' => $this->get('slugify')->slugify($game->getName()),
                'game' => $game->getId()
            ]);
        }

        $participants = $this->get('knp_paginator')->paginate($rankingParticipants, $request->query->getInt('page', 1), 100);
        $rankingLevels = $em->getRepository(RankingLevel::class)->findBy(['activated' => true], ['eloMax' => 'ASC']);

        return [
            'rankingLevels' => $rankingLevels,
            'participants' => $participants,
            'ranking' => $ranking,
            'game' => $game,
            'mode' => $mode
        ];
    }

    /**
     * @Route("/{slug}/{game}")
     * @Template()
     */
    public function gameAction(Game $game)
    {
        $em = $this->getDoctrine()->getManager();
        $modes = $em->getRepository(Mode::class)->findBy(['game' => $game, 'activated' => true]);
        $rankingsByMode = [];

        foreach ($modes as $key => $mode) {
            if (null !== $rankings = $em->getRepository(Ranking::class)->findBy(['mode' => $mode], ['id' => 'DESC'])) {
                $rankingsByMode[$mode->getId()] = $rankings;

                continue;
            }

            unset($modes[$key]);
        }

        return ['game' => $game, 'modes' => $modes, 'rankingsByMode' => $rankingsByMode];
    }

    /**
     * @return Response
     */
    public function menuAction()
    {
        if (null === $games = $this->getDoctrine()->getRepository(Mode::class)->getPlayedGames()) {
            return new Response('');
        }

        $slugify = $this->get('slugify');
        $list = null;
        foreach ($games as $game) {
            $url = $this->generateUrl('tournament_ranking_game', [
                'slug' => $slugify->slugify($game->getName()),
                'game' => $game->getId()
            ]);
            $list .= '<li><a href=\''.$url.'\'>'.$game->getName().'</a></li>';
        }

        if (null === $list) {
            return new Response('');
        }

        return new Response('
            <li class=\'dropdown\'>
                <a href=\'#\' class=\'dropdown-toggle\'>'.$this->get('translator')->trans('header.menu.ranking').'</a>
                <ul class=\'dropdown-menu default\'>'.$list.'</ul>
            </li>
        ');
    }
}
