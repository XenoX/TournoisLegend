<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TournamentBundle\Entity\LanParticipant;
use TournamentBundle\Entity\Lan;
use TournamentBundle\Workflow\LanParticipantWorkflow;

/**
 * @Route("/tournament/lan/participant")
 */
class LanParticipantController extends Controller
{
    /**
     * @Route("/state/change/{participant}")
     */
    public function changeStateAction(LanParticipant $participant)
    {
        $em = $this->getDoctrine()->getManager();

        if (LanParticipantWorkflow::STATE_REGISTERED === $participant->getState()) {
            $this->get('state_machine.lan_participant')->apply($participant, LanParticipantWorkflow::TRANS_CONFIRM);
        } else {
            $participant->setState(LanParticipantWorkflow::STATE_REGISTERED);
        }

        $em->flush();

        return $this->redirectToRoute('admin_lan_manage', ['lan' => $participant->getLan()->getId()]);
    }

    /**
     * @Route("/remove/{participant}")
     */
    public function removeAction(LanParticipant $participant)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($participant);
        $em->flush();

        return $this->redirectToRoute('admin_lan_manage', ['lan' => $participant->getLan()->getId()]);
    }
}
