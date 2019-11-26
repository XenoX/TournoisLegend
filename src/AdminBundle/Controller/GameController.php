<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\GameType;
use AppBundle\Entity\Game;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/game")
 */
class GameController extends Controller
{
    /**
     * @Route("/update/{game}")
     * @Route("/create", name="admin_game_create")
     * @Template()
     */
    public function updateAction(Request $request, Game $game = null)
    {
        $game ?: $game = new Game();

        $form = $this->createForm(GameType::class, $game);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $game->upload();
            $game->uploadBanner();

            $em->persist($game);
            $em->flush();

            $this->addFlash('success', 'Game add or updated successfully!');

            return $this->redirectToRoute('admin_admin_data');
        }

        return ['game' => $game, 'form' => $form->createView()];
    }
}
