<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\ModeType;
use AppBundle\Entity\Mode;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/game/mode")
 */
class ModeController extends Controller
{
    /**
     * @Route("/update/{mode}")
     * @Route("/create", name="admin_mode_create")
     * @Template()
     */
    public function updateAction(Request $request, Mode $mode = null)
    {
        $mode ?: $mode = new Mode();

        $form = $this->createForm(ModeType::class, $mode);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $mode->uploadBanner();

            $em->persist($mode);
            $em->flush();

            $this->addFlash('success', 'Game mode add or updated successfully!');

            return $this->redirectToRoute('admin_admin_data');
        }

        return ['mode' => $mode, 'form' => $form->createView()];
    }
}
