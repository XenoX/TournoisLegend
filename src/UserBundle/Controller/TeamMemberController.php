<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\Notification;
use UserBundle\Entity\NotificationTemplate;
use UserBundle\Entity\Team;
use UserBundle\Entity\TeamMember;

/**
 * @Route("/team/member")
 */
class TeamMemberController extends Controller
{
    /**
     * @Route("/fire/{teamMember}")
     */
    public function removeAction(TeamMember $teamMember)
    {
        $team = $teamMember->getTeam();
        $leader = $team->getLeader();

        if ($this->getUser()->getId() !== $leader->getId()) {
            return $this->redirectToRoute('app_app_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($teamMember);
        $em->flush();

        $this->addFlash('success', 'alert.success.team_member.remove');

        return $this->redirectToRoute('user_team_manage', ['team' => $team->getId()]);
    }

    /**
     * @Route("/left/{teamMember}")
     */
    public function leftAction(TeamMember $teamMember)
    {
        $user = $this->getUser();

        if (null === $user ||
            $teamMember->getUser()->getId() !== $user->getId() ||
            $teamMember::ROLE_LEADER === $teamMember->getRole()
        ) {
            return $this->redirectToRoute('app_app_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($teamMember);
        $em->flush();

        $this->addFlash('success', 'alert.success.team_member.left');

        return $this->redirectToRoute('user_team_profile', [
            'team' => $teamMember->getTeam()->getId(),
            'slug' => $this->get('slugify')->slugify($teamMember->getTeam()->getName())
        ]);
    }

    /**
     * @Route("/leader/{teamMember}")
     */
    public function leaderAction(TeamMember $teamMember)
    {
        $team = $teamMember->getTeam();
        $leader = $team->getLeader();

        if ($this->getUser()->getId() !== $leader->getId()) {
            return $this->redirectToRoute('app_app_index');
        }

        $team->setLeader($teamMember->getUser());
        $teamMember->setRole(TeamMember::ROLE_LEADER);

        $em = $this->getDoctrine()->getManager();
        $pastLeader = $em->getRepository(TeamMember::class)->findOneBy(['user' => $leader->getId(), 'team' => $team]);
        $pastLeader->setRole(TeamMember::ROLE_PLAYER);

        $this->addFlash('success', 'alert.success.team_member.leader');
        $this->get('user.notification')->add(
            $teamMember->getUser(), Notification::TYPE_TEAM, NotificationTemplate::TEAM_MEMBER_LEADER, $team->getId()
        );

        $em->flush();

        return $this->redirectToRoute('user_team_manage', ['team' => $team->getId()]);
    }

    /**
     * @Route("/join/{token}")
     * @ParamConverter("team", options={"mapping": {"token": "token"}})
     */
    public function joinAction(Team $team)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $alreadyMember = $em->getRepository(TeamMember::class)->findOneBy(['user' => $user, 'team' => $team]);
        if (null !== $alreadyMember) {
            return $this->redirectToRoute('app_app_index');
        }

        $teamMember = new TeamMember();
        $teamMember
            ->setUser($user)
            ->setTeam($team)
        ;

        $em->persist($teamMember);

        $this->addFlash('success', 'alert.success.team_member.join');
        $this->get('user.notification')->add(
            $team->getLeader(), Notification::TYPE_TEAM, NotificationTemplate::TEAM_MEMBER_JOINED, $team->getId()
        );

        $em->flush();

        return $this->redirectToRoute('user_team_profile', [
            'team' => $team->getId(),
            'slug' => $this->get('slugify')->slugify($team->getName())
        ]);
    }

    /**
     * @Route("/request/add/{team}")
     */
    public function requestAddAction(Team $team)
    {
        $em = $this->getDoctrine()->getManager();
        $userRequested = $em->getRepository(TeamMember::class)->findOneBy([
            'user' => $this->getUser(),
            'team' => $team,
            'requested' => true
        ]);

        if ($userRequested) {
            $this->addFlash('warning', 'alert.warning.team_member.already_requested');

            return $this->redirectToRoute('user_team_profile', [
                'team' => $team->getId(),
                'slug' => $this->get('slugify')->slugify($team->getName())
            ]);
        }

        $teamMember = new TeamMember();
        $teamMember
            ->setUser($this->getUser())
            ->setTeam($team)
            ->setRequested(true)
        ;

        $em->persist($teamMember);

        $this->get('user.notification')->add(
            $team->getLeader(), Notification::TYPE_TEAM, NotificationTemplate::TEAM_MEMBER_REQUESTED, $team->getId()
        );

        $em->flush();

        $this->addFlash('success', 'alert.success.team_member.request');

        return $this->redirectToRoute('user_team_profile', [
            'team' => $team->getId(),
            'slug' => $this->get('slugify')->slugify($team->getName())
        ]);
    }

    /**
     * @Route("/request/delete/{teamMember}")
     */
    public function requestDeleteAction(TeamMember $teamMember)
    {
        if (false === $teamMember->getRequested()) {
            return $this->redirectToRoute('app_app_index');
        }

        $user = $this->getUser();
        if ($user === $teamMember->getTeam()->getLeader() || $user === $teamMember->getUser()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($teamMember);
            $em->flush();

            $this->addFlash('success', 'alert.success.team_member.request_cancel');

            if ($user === $teamMember->getUser()) {
                $team = $teamMember->getTeam();

                return $this->redirectToRoute('user_team_profile', [
                    'team' => $team->getId(),
                    'slug' => $this->get('slugify')->slugify($team->getName())
                ]);
            }
        }

        return $this->redirectToRoute('user_team_manage', ['team' => $teamMember->getTeam()->getId()]);
    }

    /**
     * @Route("/request/accept/{teamMember}")
     */
    public function requestAcceptAction(TeamMember $teamMember)
    {
        if (false === $teamMember->getRequested() || $this->getUser() !== $teamMember->getTeam()->getLeader()) {
            return $this->redirectToRoute('app_app_index');
        }

        $em = $this->getDoctrine()->getManager();

        $teamMember
            ->setRequested(false)
            ->setJoinAt(new \DateTime())
        ;

        $this->get('user.notification')->add(
            $teamMember->getUser(), Notification::TYPE_TEAM, NotificationTemplate::TEAM_MEMBER_ACCEPTED, $teamMember->getTeam()->getId()
        );

        $em->flush();

        return $this->redirectToRoute('user_team_manage', ['team' => $teamMember->getTeam()->getId()]);
    }
}
