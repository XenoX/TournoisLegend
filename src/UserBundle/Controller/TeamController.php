<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TournamentBundle\Entity\Participant;
use TournamentBundle\Entity\RankingLevel;
use TournamentBundle\Entity\RankingParticipant;
use UserBundle\Entity\Notification;
use UserBundle\Entity\NotificationTemplate;
use UserBundle\Entity\Team;
use UserBundle\Entity\TeamMember;
use UserBundle\Form\TeamCreateType;
use UserBundle\Form\TeamManageType;

/**
 * @Route("/team")
 */
class TeamController extends Controller
{
    /**
     * @Route("/create")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $team = new Team();
        $form = $this->createForm(TeamCreateType::class, $team);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $team->setLeader($user);

            $em->persist($team);

            $teamMember = new TeamMember();
            $teamMember
                ->setRole(TeamMember::ROLE_LEADER)
                ->setTeam($team)
                ->setUser($user)
            ;

            $em->persist($teamMember);

            $em->flush();

            $this->addFlash('success', 'alert.success.team_create');

            return $this->redirectToRoute('user_team_profile', [
                'team' => $team->getId(),
                'slug' => $this->get('slugify')->slugify($team->getName())
            ]);
        }

        return ['team' => $team, 'form' => $form->createView()];
    }

    /**
     * @Route("/profile/{team}-{slug}", defaults={"slug": null})
     * @Template()
     */
    public function profileAction(Request $request, Team $team)
    {
        if (false === $team->isClean()) {
            return $this->redirectToRoute('app_app_index');
        }

        $em = $this->getDoctrine()->getManager();
        $teamMemberRepo = $em->getRepository(TeamMember::class);

        $teamMembers = $teamMemberRepo->findBy(['team' => $team, 'requested' => false]);
        $userInTeam = $teamMemberRepo->findOneBy(['team' => $team, 'user' => $this->getUser()]);
        $members = $this->get('knp_paginator')->paginate($teamMembers, $request->query->getInt('page', 1), 8);

        $rankingParticipants = $em->getRepository(RankingParticipant::class)->getRankingParticipantsForTeam($team);
        $lastTournaments = $em->getRepository(Participant::class)->findProfileLastTournaments(null, $team, 5);
        $upcomingTournaments = $em->getRepository(Participant::class)->findProfileNextTournaments(null, $team, 5);

        $rankingLevels = $em->getRepository(RankingLevel::class)->findBy(['activated' => true], ['eloMax' => 'ASC']);

        return [
            'team' => $team,
            'members' => $members,
            'userInTeam' => $userInTeam,
            'rankingParticipants' => $rankingParticipants,
            'lastTournaments' => $lastTournaments,
            'upcomingTournaments' => $upcomingTournaments,
            'rankingLevels' => $rankingLevels
        ];
    }

    /**
     * @Route("/manage/{team}")
     * @Template()
     */
    public function manageAction(Request $request, Team $team)
    {
        if (false === $team->isClean()) {
            return $this->redirectToRoute('app_app_index');
        }

        $em = $this->getDoctrine()->getManager();
        $teamMemberRepo = $em->getRepository(TeamMember::class);

        $userInTeam = $teamMemberRepo->findOneBy(['user' => $this->getUser(), 'team' => $team]);
        if (null === $userInTeam) {
            return $this->redirectToRoute('app_app_index');
        }

        $teamMembers = $teamMemberRepo->findBy(['team' => $team, 'requested' => false]);
        $requests = $teamMemberRepo->findBy(['team' => $team, 'requested' => true]);

        $paginator = $this->get('knp_paginator');
        $members = $paginator->paginate(
            $teamMembers,
            $request->query->getInt('page', 1),
            6
        );

        $form = $this->createForm(TeamManageType::class, $team);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $team->upload();
            $team->uploadBanner();

            $em->flush();

            $this->addFlash('success', 'alert.success.team_edit');

            return $this->redirectToRoute('user_team_manage', ['team' => $team->getId()]);
        }

        return ['team' => $team, 'members' => $members, 'requests' => $requests, 'form' => $form->createView(), 'teamMember' => $userInTeam];
    }

    /**
     * @Route("/delete/{team}")
     */
    public function deleteAction(Team $team)
    {
        if (false === $team->isClean()) {
            return $this->redirectToRoute('app_app_index');
        }

        if ($team->getLeader() === $this->getUser()) {
            $em = $this->getDoctrine()->getManager();

            $team->setName($team->getName().'_DELETED_'.(new \DateTimeImmutable())->format('Ymdhis'));
            $team->setDeleted(true);
            $em->flush();

            $teamMembers = $em->getRepository(TeamMember::class)->findBy(['team' => $team, 'requested' => false]);
            foreach ($teamMembers as $member) {
                $this->get('user.notification')->add(
                    $member->getUser(), Notification::TYPE_TEAM, NotificationTemplate::TEAM_DELETED
                );
            }

            $this->addFlash('success', 'alert.success.remove_team');

            $em->flush();
        }

        return $this->redirectToRoute('app_app_index');
    }

    /**
     * @Route("/{type}/remove/{team}")
     */
    public function removeImageAction($type = 'logo', Team $team)
    {
        if (false === $team->isClean()) {
            return $this->redirectToRoute('app_app_index');
        }

        if ($this->getUser() && $this->getUser()->getId() === $team->getLeader()->getId()) {
            if ('logo' === $type) {
                $team->setLogo(Team::DEFAULT_LOGO);
                $this->addFlash('success', 'alert.success.remove_logo');
            } elseif ('banner' === $type) {
                $team->setBanner(Team::DEFAULT_BANNER);
                $this->addFlash('success', 'alert.success.remove_banner');
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }

        return $this->redirectToRoute('user_user_settings');
    }

    public function menuAction()
    {
        $user = $this->getUser();
        if (!$user) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();
        $teamsMember = $em->getRepository(TeamMember::class)->findBy([
            'user' => $this->getUser()->getId(),
            'requested' => false
        ]);

        if ($teamsMember) {
            $slugify = $this->get('slugify');
            $list = null;
            foreach ($teamsMember as $teamMember) {
                $team = $teamMember->getTeam();
                if ($team->isClean()) {
                    $teamName = $team->getName();
                    $url = $this->generateUrl('user_team_profile', [
                        'team' => $team->getId(),
                        'slug' => $slugify->slugify($teamName)
                    ]);
                    $list .= '<li><a href=\''.$url.'\'>'.$teamName.'</a></li>';
                }
            }
            if (null !== $list) {
                return new Response($list.'<li class=\'divider\'></li>');
            }
        }

        return new Response('');
    }
}
