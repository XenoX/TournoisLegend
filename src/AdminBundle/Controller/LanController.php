<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use TournamentBundle\Entity\Lan;
use AdminBundle\Form\LanType;
use Symfony\Component\HttpFoundation\Request;
use TournamentBundle\Entity\LanParticipant;

/**
 * @Route("/tournament/lan")
 */
class LanController extends Controller
{
    /**
     * @Route("/update/{lan}")
     * @Route("/create", name="admin_lan_create")
     * @Template
     */
    public function updateAction(Request $request, Lan $lan = null)
    {
        $lan ?: $lan = new Lan();

        $form = $this->createForm(LanType::class, $lan);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($lan);
            $em->flush();

            $this->addFlash('success', 'Lan add or updated successfully!');

            return $this->redirect($this->generateUrl('admin_admin_lan'));
        }

        return ['form' => $form->createView(), 'lan' => $lan];
    }

    /**
     * @Route("/manage/{lan}")
     * @Template
     */
    public function manageAction(Lan $lan)
    {
        return [
            'lan' => $lan,
            'participants' => $this->getDoctrine()->getRepository(LanParticipant::class)->findBy(['lan' => $lan->getId()])
        ];
    }
}
