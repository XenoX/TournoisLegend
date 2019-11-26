<?php

namespace TournamentBundle\Controller;

use AppBundle\Entity\Tag;
use Symfony\Component\HttpFoundation\Response;
use TournamentBundle\Entity\Lan;
use TournamentBundle\Entity\LanParticipant;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TournamentBundle\Form\LanRegistrationType;

/**
 * @Route("/lan")
 */
class LanController extends Controller
{
    /**
     * @Route("/{lan}-{slug}")
     * @Template
     */
    public function viewAction(Request $request, Lan $lan)
    {
        $em = $this->getDoctrine()->getManager();
        $tag = $em->getRepository(Tag::class)->findOneBy(['game' => $lan->getGame()]);
        $participants = $em->getRepository(LanParticipant::class)->findBy(['lan' => $lan]);

        $form = $this->createForm(LanRegistrationType::class, ['tag' => $tag->getName(), 'lan' => $lan]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $form->get('email')->getData();

            $participant = new LanParticipant();
            $participant
                ->setName($form->get('name')->getData())
                ->setLan($lan)
                ->setEmail($email)
            ;

            if ($lan->isTeam()) {
                $playersString = '';
                for ($i = 0; $i < $lan->getFormat(); $i++) {
                    $playersString .= $data['username_' . $i] . ', ';
                }

                $participant->setPlayers(substr($playersString, 0, -2));
            }

            $this->get('app.mail')->send(
                null,
                [['name' => $form->get('name')->getData(), 'email' => $email]],
                $lan->getMail()->getSubject(),
                strtr($lan->getMail()->getContent(), [
                    '%name%' => $participant->getName(),
                    '%email%' => $participant->getEmail(),
                    '%players%' => $participant->getPlayers()
                ])
            );

            $this->addFlash('success', 'Inscription réalisée avec succès !<br>Vous allez bientôt recevoir un email de confirmation<br>Merci à vous !');

            $em->persist($participant);
            $em->flush();

            return $this->redirect($this->generateUrl('tournament_lan_view', ['lan' => $lan->getId(), 'slug' => $lan->getSlug()]));
        }

        return ['lan' => $lan, 'participants' => $participants, 'form' => $form->createView()];
    }

    /**
     * @return Response
     */
    public function menuAction()
    {
        if (null === $lans = $this->getDoctrine()->getRepository(Lan::class)->findBy(['activated' => true])) {
            return new Response('');
        }

        $slugify = $this->get('slugify');
        $list = null;
        foreach ($lans as $lan) {
            $url = $this->generateUrl('tournament_lan_view', [
                'lan' => $lan->getId(),
                'slug' => $slugify->slugify($lan->getName())
            ]);
            $list .= '<li><a href=\''.$url.'\'>'.$lan->getName().'</a></li>';
        }

        if (null === $list) {
            return new Response('');
        }

        return new Response('
            <li class=\'dropdown\'>
                <a href=\'#\' class=\'dropdown-toggle\'>'.$this->get('translator')->trans('header.menu.lan').'</a>
                <ul class=\'dropdown-menu default\'>'.$list.'</ul>
            </li>
        ');
    }
}
