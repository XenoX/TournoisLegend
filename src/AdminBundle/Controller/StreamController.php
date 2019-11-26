<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Workflow\Exception\LogicException;
use TournamentBundle\Entity\Stream;
use TournamentBundle\Workflow\StreamWorkflow;
use UserBundle\Entity\Notification;
use UserBundle\Entity\NotificationTemplate;

/**
 * @Route("/tournament/stream")
 */
class StreamController extends Controller
{
    /**
     * @Route("/main/{stream}")
     */
    public function mainAction(Stream $stream)
    {
        $em = $this->getDoctrine()->getManager();
        $tournament = $stream->getTournament();

        $streamsDatabase = $em->getRepository(Stream::class)->findBy(['tournament' => $tournament]);
        foreach ($streamsDatabase as $streamDatabase) {
            $streamDatabase->setMain(false);
        }

        $stream->setMain(true);

        $em->flush();

        return $this->redirectToRoute('admin_tournament_manage', ['tournament' => $tournament->getId()]);
    }

    /**
     * @Route("/accept/{stream}")
     */
    public function acceptAction(Stream $stream)
    {
        $em = $this->getDoctrine()->getManager();
        $tournament = $stream->getTournament();

        try {
            $this->get('state_machine.stream')->apply($stream, StreamWorkflow::TRANS_ACCEPT);
        } catch (LogicException $exception) {
            $this->addFlash('danger', $exception->getMessage());

            return $this->redirectToRoute('admin_tournament_manage', ['tournament' => $tournament->getId()]);
        }

        $this->get('user.notification')->add(
            $stream->getUser(),
            Notification::TYPE_TOURNAMENT,
            NotificationTemplate::TOURNAMENT_STREAM_ACCEPTED,
            $tournament->getId()
        );

        $em->flush();

        return $this->redirectToRoute('admin_tournament_manage', ['tournament' => $tournament->getId()]);
    }

    /**
     * @Route("/refuse/{stream}")
     */
    public function refuseAction(Stream $stream)
    {
        $em = $this->getDoctrine()->getManager();
        $tournament = $stream->getTournament();

        try {
            $this->get('state_machine.stream')->apply($stream, StreamWorkflow::TRANS_REFUSE);
        } catch (LogicException $exception) {
            $this->addFlash('danger', $exception->getMessage());

            return $this->redirectToRoute('admin_tournament_manage', ['tournament' => $tournament->getId()]);
        }

        $em->flush();

        return $this->redirectToRoute('admin_tournament_manage', ['tournament' => $tournament->getId()]);
    }
}
