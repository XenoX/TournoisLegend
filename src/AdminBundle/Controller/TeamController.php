<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\TeamType;
use UserBundle\Entity\Notification;
use UserBundle\Entity\NotificationTemplate;
use UserBundle\Entity\Team;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\TeamMember;

/**
 * @Route("/team")
 */
class TeamController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $findTeams = $this->getDoctrine()->getRepository(Team::class)->createQueryBuilder('t')->addOrderBy('t.id', 'DESC');

        $teams = $this->get('knp_paginator')->paginate(
            $findTeams,
            $request->query->getInt('page', 1),
            100
        );

        return ['teams' => $teams];
    }

    /**
     * @Route("/update/{team}")
     * @Template()
     */
    public function updateAction(Request $request, Team $team)
    {
        $form = $this->createForm(TeamType::class, $team);

        $em = $this->getDoctrine()->getManager();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $team->upload();
            $team->uploadBanner();

            $em->flush();
            $this->addFlash('success', 'Team edit successfully!');

            return $this->redirectToRoute('admin_team_index');
        }

        $members = $em->getRepository(TeamMember::class)->findBy(['team' => $team]);

        return ['team' => $team, 'members' => $members, 'form' => $form->createView()];
    }

    /**
     * @Route("/quit/{teamMember}")
     */
    public function quitAction(TeamMember $teamMember)
    {
        $team = $teamMember->getTeam();

        if ($team->getLeader()->getId() === $teamMember->getUser()->getId()) {
            $this->addFlash('danger', 'This member is the leader');

            return $this->redirectToRoute('admin_team_update', ['team' => $team->getId()]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($teamMember);

        $em->flush();

        $this->addFlash('success', 'Member removed successfully!');

        return $this->redirectToRoute('admin_team_update', ['team' => $team->getId()]);
    }

    /**
     * @Route("/leader/{teamMember}")
     */
    public function leaderAction(TeamMember $teamMember)
    {
        $team = $teamMember->getTeam();
        $leader = $team->getLeader();

        $team->setLeader($teamMember->getUser());
        $teamMember->setRole(TeamMember::ROLE_LEADER);

        $em = $this->getDoctrine()->getManager();
        $pastLeader = $em->getRepository(TeamMember::class)->findOneBy(['user' => $leader->getId(), 'team' => $team]);
        $pastLeader->setRole(TeamMember::ROLE_PLAYER);

        $this->addFlash('success', 'Leader set successfully!');
        $this->get('user.notification')->add(
            $teamMember->getUser(),
            Notification::TYPE_TEAM,
            NotificationTemplate::ADMIN_TEAM_MEMBER_LEADER,
            $team->getId()
        );

        $em->flush();

        return $this->redirectToRoute('admin_team_update', ['team' => $team->getId()]);
    }

    /**
     * @Route("/{type}/remove/{team}")
     */
    public function removeImageAction($type = 'logo', Team $team)
    {
        if ('logo' === $type) {
            $team->setLogo(Team::DEFAULT_LOGO);
            $this->addFlash('success', 'Logo removed successfully!');
        } elseif ('banner' === $type) {
            $team->setBanner(Team::DEFAULT_BANNER);
            $this->addFlash('success', 'Banner removed successfully!');
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('admin_team_update', ['team' => $team->getId()]);
    }
}
