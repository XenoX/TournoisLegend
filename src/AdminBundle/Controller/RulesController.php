<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\RulesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TournamentBundle\Entity\Rules;

/**
 * @Route("/tournament/rule")
 */
class RulesController extends Controller
{
    /**
     * @Route("/update/{rules}")
     * @Route("/create", name="admin_rules_create")
     * @Template()
     */
    public function updateAction(Request $request, Rules $rules = null)
    {
        $rules ?: $rules = new Rules();

        $form = $this->createForm(RulesType::class, $rules);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($rules);
            $em->flush();

            $this->addFlash('success', 'Rules add or updated successfully!');

            return $this->redirectToRoute('admin_admin_tournament');
        }

        return ['rules' => $rules, 'form' => $form->createView()];
    }
}
