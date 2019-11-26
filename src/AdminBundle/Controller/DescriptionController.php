<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\DescriptionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TournamentBundle\Entity\Description;

/**
 * @Route("/tournament/description")
 */
class DescriptionController extends Controller
{
    /**
     * @Route("/update/{description}")
     * @Route("/create", name="admin_description_create")
     * @Template()
     */
    public function updateAction(Request $request, Description $description = null)
    {
        $description ?: $description = new Description();

        $form = $this->createForm(DescriptionType::class, $description);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($description);
            $em->flush();

            $this->addFlash('success', 'Description add or updated successfully!');

            return $this->redirectToRoute('admin_admin_tournament');
        }

        return ['description' => $description, 'form' => $form->createView()];
    }
}
