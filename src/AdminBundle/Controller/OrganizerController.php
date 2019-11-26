<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\OrganizerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TournamentBundle\Entity\Organizer;

/**
 * @Route("/tournament/organizer")
 */
class OrganizerController extends Controller
{
    /**
     * @Route("/update/{organizer}")
     * @Route("/create", name="admin_organizer_create")
     * @Template()
     */
    public function updateAction(Request $request, Organizer $organizer = null)
    {
        $organizer ?: $organizer = new Organizer();

        $form = $this->createForm(OrganizerType::class, $organizer);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $organizer->upload();

            $em->persist($organizer);
            $em->flush();

            $this->addFlash('success', 'Organizer add or updated successfully!');

            return $this->redirectToRoute('admin_admin_tournament');
        }

        return ['organizer' => $organizer, 'form' => $form->createView()];
    }
}
