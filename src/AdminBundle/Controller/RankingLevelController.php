<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\RankingLevelType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TournamentBundle\Entity\RankingLevel;

/**
 * Class RankingLevelController
 *
 * @Route("/ranking-level")
 *
 * @package AdminBundle\Controller
 */
class RankingLevelController extends Controller
{
    /**
     * @Route("/update/{rankingLevel}")
     * @Route("/create", name="admin_rankinglevel_create")
     * @Template()
     */
    public function updateAction(Request $request, RankingLevel $rankingLevel = null)
    {
        $rankingLevel ?: $rankingLevel = new RankingLevel();

        $form = $this->createForm(RankingLevelType::class, $rankingLevel);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $rankingLevel->upload();

            $em->persist($rankingLevel);
            $em->flush();

            $this->addFlash('success', 'Ranking level add or updated successfully!');

            return $this->redirectToRoute('admin_admin_ranking');
        }

        return ['rankingLevel' => $rankingLevel, 'form' => $form->createView()];
    }
}
