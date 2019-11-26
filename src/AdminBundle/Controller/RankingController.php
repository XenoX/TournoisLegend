<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\RankingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TournamentBundle\Entity\Ranking;

/**
 * Class RankingController
 *
 * @Route("/ranking")
 *
 * @package AdminBundle\Controller
 */
class RankingController extends Controller
{
    /**
     * @Route("/update/{ranking}")
     * @Route("/create", name="admin_ranking_create")
     * @Template()
     */
    public function updateAction(Request $request, Ranking $ranking = null)
    {
        $ranking ?: $ranking = new Ranking();

        $form = $this->createForm(RankingType::class, $ranking);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($ranking);
            $em->flush();

            $this->addFlash('success', 'Ranking add or updated successfully!');

            return $this->redirectToRoute('admin_admin_ranking');
        }

        return ['ranking' => $ranking, 'form' => $form->createView()];
    }
}
