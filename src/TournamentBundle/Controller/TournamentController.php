<?php

namespace TournamentBundle\Controller;

use AppBundle\Entity\Game;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TournamentBundle\Entity\Alert;
use TournamentBundle\Entity\Battle;
use TournamentBundle\Entity\Participant;
use TournamentBundle\Entity\Stream;
use TournamentBundle\Entity\Tournament;
use TournamentBundle\Form\StreamRequestType;
use TournamentBundle\Repository\TournamentRepository;
use TournamentBundle\Workflow\ParticipantWorkflow;
use TournamentBundle\Workflow\StreamWorkflow;
use TournamentBundle\Workflow\TournamentWorkflow;
use UserBundle\Entity\Team;
use UserBundle\Entity\User;

class TournamentController extends Controller
{
    /**
     * @Route("/")
     * @Route("/{game}-{slug}", name="tournament_tournament_index_game")
     * @Template()
     */
    public function indexAction(Request $request, Game $game = null)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var TournamentRepository $tournamentRepo */
        $tournamentRepo = $em->getRepository(Tournament::class);
        $games = $em->getRepository(Game::class)->findBy(['activated' => true]);

        $tournamentsNotStarted = $em->getRepository(Tournament::class)->findBy(['state' => TournamentWorkflow::STATES_BEFORE_CHECKIN]);
        $tournamentService = $this->get('tournament');
        foreach ($tournamentsNotStarted as $tournamentToCheck) {
            if (true === $tournamentService->changeStateIfNeeded($tournamentToCheck)) {
                $this->getDoctrine()->getManager()->flush();
            }
        }

        $em->flush();

        $tournamentsInProgress = $tournamentRepo->findByStateAndGameTournaments(TournamentWorkflow::STATE_MATCH, $game);
        $tournaments = $this->get('knp_paginator')->paginate(
            $tournamentRepo->findTournaments($game), $request->query->getInt('page', 1), 7
        );
        $tournamentsLast = $tournamentRepo->findLastTournaments($game, 3);

        return [
            'games' => $games,
            'tournaments' => $tournaments,
            'tournamentsInProgress' => $tournamentsInProgress,
            'tournamentsLast' => $tournamentsLast,
            'game' => $game ?? new Game()
        ];
    }

    /**
     * @Route("/view/{game}/{tournament}-{slug}")
     * @Template()
     */
    public function profileAction(Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();

        if (true === $this->get('tournament')->changeStateIfNeeded($tournament)) {
            $em->flush();
        }

        $participantService = $this->get('tournament.participant');
        /** @var User $user */
        $user = $this->getUser();

        $actionPossibility = $participantService->getPossibility($user, $tournament);
        $participants = $tournament->getHiddenParticipant() ? null : $participantService->getTournamentParticipants($tournament);
        $participant = $em->getRepository(Participant::class)->findOneBy(['tournament' => $tournament, 'user' => $user]);

        $streams = $em->getRepository(Stream::class)->findBy(
            ['tournament' => $tournament, 'state' => StreamWorkflow::STATE_ACCEPTED],
            ['main' => 'DESC']
        );

        $lastChannelName = null;
        if ($user) {
            $lastChannelName = $user->getTwitch();
            if (null === $lastChannelName) {
                if ($lastChannelName = $em->getRepository(Stream::class)->findOneBy(['user' => $user], ['id' => 'DESC'])) {
                    $lastChannelName = $lastChannelName->getChannel();
                }
            }
        }

        $formStream = $this->createForm(
            StreamRequestType::class,
            [new Stream(), 'lastChannelName' => $lastChannelName],
            ['action' => $this->generateUrl('tournament_stream_request', ['tournament' => $tournament->getId()])]
        );

        /** @var Alert[] $alerts */
        $alerts = $em->getRepository(Alert::class)->findBy(['tournament' => $tournament, 'activated' => true]);

        return [
            'tournament' => $tournament,
            'participants' => $participants,
            'participant' => $participant,
            'actionPossibility' => $actionPossibility,
            'streams' => $streams,
            'alerts' => $alerts,
            'formStream' => $formStream->createView()
        ];
    }

    /**
     * @Template("@Tournament/Include/tournament_menu.html.twig")
     */
    public function menuAction()
    {
        return [
            'games' => $this->getDoctrine()->getRepository(Game::class)->findBy(['activated' => true]),
            'game' => new Game()
        ];
    }

    // AJAX

    /**
     * @Route("/ajax/bracket/{tournament}", condition="request.isXmlHttpRequest()")
     * @Template("@Tournament/Include/tournament_bracket.html.twig")
     */
    public function bracketAction(Tournament $tournament)
    {
        $bracketService = $this->get('tournament.bracket');

        $teams = json_encode($bracketService->getFirstRoundParticipantBattle($tournament));
        $results = json_encode($bracketService->getBattleResults($tournament));

        return ['teams' => $teams, 'results' => $results, 'tournament' => $tournament];
    }

    /**
     * @Route("/ajax/battle/{tournament}/{participant}", condition="request.isXmlHttpRequest()")
     * @Template("@Tournament/Include/tournament_battle.html.twig")
     */
    public function battleAction(Tournament $tournament, Participant $participant)
    {
        $em = $this->getDoctrine()->getManager();

        $participants = [$participant];
        if ($tournament->isTeam()) {
            $participants = $em->getRepository(Participant::class)->findBy(['tournament' => $tournament, 'team' => $participant->getTeam()]);
        }

        $battle = $em->getRepository(Battle::class)->findOneBy(
            ['tournament' => $tournament, 'participant1' => $participants, 'score1' => 0, 'score2' => 0, 'endAt' => null],
            ['battleId' => 'DESC']
        );
        if (null === $battle) {
            $battle = $em->getRepository(Battle::class)->findOneBy(
                ['tournament' => $tournament, 'participant2' => $participants, 'score1' => 0, 'score2' => 0, 'endAt' => null],
                ['battleId' => 'DESC']
            );
        }

        if (null !== $battle && $tournament->isTeam() && ($tournament->getSize() - 8) < $battle->getBattleId()) {
            $totalParticipantsTeam = $em->getRepository(Participant::class)->count(['tournament' => $tournament, 'team' => $participant->getTeam()]);
        }

        return [
            'tournament' => $tournament,
            'participant' => $participant,
            'battle' => $battle,
            'totalParticipantsTeam' => $totalParticipantsTeam ?? null
        ];
    }
}
