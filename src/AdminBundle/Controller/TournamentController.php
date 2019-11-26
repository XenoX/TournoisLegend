<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\History;
use AdminBundle\Form\AlertType;
use AdminBundle\Form\TournamentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Workflow\Exception\LogicException;
use TournamentBundle\Entity\Alert;
use TournamentBundle\Entity\Battle;
use TournamentBundle\Entity\Participant;
use TournamentBundle\Entity\Stream;
use TournamentBundle\Entity\Tournament;
use TournamentBundle\Workflow\TournamentWorkflow;

/**
 * @Route("/tournament")
 */
class TournamentController extends Controller
{
    /**
     * @Route("/update/{tournament}")
     * @Route("/create", name="admin_tournament_create")
     * @Template()
     */
    public function updateAction(Request $request, Tournament $tournament = null)
    {
        $tournament ?: $tournament = new Tournament();

        $form = $this->createForm(TournamentType::class, $tournament);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($tournament);

            $em->flush();

            $this->get('admin.history')->add(
                $tournament->getId(),
                History::TOURNAMENT,
                '[Admin] Tournament updated',
                $this->getUser()
            );

            $em->flush();

            $this->addFlash('success', 'Tournament add or updated successfully!');

            return $this->redirectToRoute('admin_admin_tournament');
        }

        return ['tournament' => $tournament, 'form' => $form->createView()];
    }

    /**
     * @Route("/manage/{tournament}")
     * @Template()
     */
    public function manageAction(Request $request, Tournament $tournament)
    {
        if (true === $this->get('tournament')->changeStateIfNeeded($tournament)) {
            $this->getDoctrine()->getManager()->flush();
        }

        $em = $this->getDoctrine()->getManager();
        $alert = new Alert();
        $form = $this->createForm(AlertType::class, $alert);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $alert->setTournament($tournament);

            $em->persist($alert);
            $em->flush();

            $this->addFlash('success', 'Alert added successfully!');

            return $this->redirectToRoute('admin_tournament_manage', ['tournament' => $tournament->getId()]);
        }

        /** @var Alert[] $alerts */
        $alerts = $em->getRepository(Alert::class)->findBy(['tournament' => $tournament]);

        if ($tournament->isDone()) {
            $size = $tournament->getSize();
            $battles = $em->getRepository(Battle::class)->findBy(
                ['tournament' => $tournament, 'battleId' => [$size, $size-1, $size-4, $size-5, $size-6, $size-7]],
                ['battleId' => 'DESC']
            );

            $podium = [
                1 => $battles[1]->getWinner(),
                2 => $battles[1]->getLoser(),
                3 => $battles[0]->getWinner(),
                4 => $battles[0]->getLoser(),
                5 => [
                    array_key_exists(2, $battles) ? $battles[2]->getLoser() : null,
                    array_key_exists(3, $battles) ? $battles[3]->getLoser() : null,
                    array_key_exists(4, $battles) ? $battles[4]->getLoser() : null,
                    array_key_exists(5, $battles) ? $battles[5]->getLoser() : null
                ]
            ];
        }

        return ['tournament' => $tournament, 'podium' => $podium ?? null, 'alerts' => $alerts, 'form' => $form->createView()];
    }

    /**
     * @Route("/start/{tournament}")
     */
    public function startAction(Tournament $tournament)
    {
        $participants = $this->get('tournament.participant')->getTournamentParticipantsToStart($tournament);

        $tournamentService = $this->get('tournament');
        if (false === $tournamentService->canStart($tournament, $participants)) {
            return $this->redirectToRoute('admin_tournament_manage', ['tournament' => $tournament->getId()]);
        }

        $tournamentService->reduceSizeIfNeeded($tournament, count($participants));
        shuffle($participants);

        $battleService = $this->get('tournament.battle');
        $battles = $battleService->getBattlesForTournamentStart($tournament, $participants);

        foreach ($battles as $battleId => $battle) {
            $battleService->add($tournament, $battle[0][0], $battle[1][0], $battleId);
        }

        try {
            $this->get('state_machine.tournament')->apply($tournament, TournamentWorkflow::TRANS_MATCH);

            $this->get('admin.history')->add(
                $tournament->getId(),
                History::TOURNAMENT,
                '[Admin] Tournament started',
                $this->getUser()
            );

            $this->getDoctrine()->getManager()->flush();
        } catch (LogicException $exception) {
            $this->addFlash('danger', $exception->getMessage());
        }

        return $this->redirectToRoute('admin_tournament_manage', ['tournament' => $tournament->getId()]);
    }

    /**
     * @Route("/stop/{tournament}")
     */
    public function stopAction(Tournament $tournament)
    {
        if (true === $this->get('tournament')->stop($tournament)) {
            $this->get('admin.history')->add(
                $tournament->getId(),
                History::TOURNAMENT,
                '[Admin] Tournament stoped',
                $this->getUser()
            );

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('admin_tournament_manage', ['tournament' => $tournament->getId()]);
    }

    /**
     * @Route("/alert/remove/{alert}")
     */
    public function removeAlertAction(Alert $alert)
    {
        $tournament = $alert->getTournament();

        $em = $this->getDoctrine()->getManager();
        $em->remove($alert);

        $em->flush();

        return $this->redirectToRoute('admin_tournament_manage', ['tournament' => $tournament->getId()]);
    }

    // AJAX

    /**
     * @Route("/ajax/streams/{tournament}", condition="request.isXmlHttpRequest()")
     * @Template("@Admin/Tournament/Include/tournament_streams.html.twig")
     */
    public function streamsAction(Tournament $tournament)
    {
        /** @var Stream[] $streams */
        $streams = $this->getDoctrine()->getRepository(Stream::class)->findBy(['tournament' => $tournament]);

        return ['streams' => $streams, 'tournament' => $tournament];
    }

    /**
     * @Route("/ajax/matchs/{tournament}", condition="request.isXmlHttpRequest()")
     * @Template("@Admin/Tournament/Include/tournament_matchs.html.twig")
     */
    public function matchsAction(Tournament $tournament)
    {
        /** @var Battle[] $battles */
        $battles = $this->getDoctrine()->getRepository(Battle::class)->findBy(['tournament' => $tournament], ['battleId' => 'DESC']);

        return ['battles' => $battles, 'tournament' => $tournament];
    }

    /**
     * @Route("/ajax/history/{tournament}", condition="request.isXmlHttpRequest()")
     * @Template("@Admin/Tournament/Include/tournament_history.html.twig")
     */
    public function historyAction(Tournament $tournament)
    {
        /** @var History[] $histories */
        $histories = $this->getDoctrine()->getRepository(History::class)->findBy(
            ['entityName' => History::TOURNAMENT, 'entityId' => $tournament->getId()],
            ['createdAt' => 'DESC']
        );

        return ['histories' => $histories, 'tournament' => $tournament];
    }

    /**
     * @Route("/ajax/participants/{tournament}", condition="request.isXmlHttpRequest()")
     * @Template("@Admin/Tournament/Include/tournament_participants.html.twig")
     */
    public function participantsAction(Tournament $tournament)
    {
        $participants = $this->get('tournament.participant')->getTournamentParticipants($tournament);
        $countCheckInParticipants = $this->getDoctrine()->getRepository(Participant::class)->countCheckInParticipantForTournament($tournament);
        if (is_array($countCheckInParticipants)) {
            $countCheckInParticipants = count($countCheckInParticipants);
        }

        return ['participants' => $participants, 'countCheckInParticipants' => $countCheckInParticipants, 'tournament' => $tournament];
    }
}
