<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\RewardType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TournamentBundle\Entity\Reward;

/**
 * @Route("/tournament/reward")
 */
class RewardController extends Controller
{
    /**
     * @Route("/update/{reward}")
     * @Route("/create", name="admin_reward_create")
     * @Template()
     */
    public function updateAction(Request $request, Reward $reward = null)
    {
        $reward ?: $reward = new Reward();

        $form = $this->createForm(RewardType::class, $reward);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($reward);
            $em->flush();

            $this->addFlash('success', 'Reward add or updated successfully!');

            return $this->redirectToRoute('admin_admin_tournament');
        }

        return ['reward' => $reward, 'form' => $form->createView()];
    }
}
