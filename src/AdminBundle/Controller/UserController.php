<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\UserNoteType;
use AdminBundle\Form\UserType;
use UserBundle\Entity\User;
use UserBundle\Entity\TeamMember;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $findUsers = $this->getDoctrine()->getRepository(User::class)->createQueryBuilder('u')->addOrderBy('u.id', 'DESC');

        $users = $this->get('knp_paginator')->paginate(
            $findUsers,
            $request->query->getInt('page', 1),
            100
        );

        return ['users' => $users];
    }

    /**
     * @Route("/update/{user}")
     * @Template()
     */
    public function updateAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(UserType::class, $user);
        $formNote = $this->createForm(UserNoteType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Member edit successfully!');

            return $this->redirectToRoute('admin_user_update', ['user' => $user->getId()]);
        }

        $formNote->handleRequest($request);
        if ($formNote->isSubmitted() && $formNote->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Note add/edit successfully!');

            return $this->redirectToRoute('admin_user_update', ['user' => $user->getId()]);
        }

        $teamsMember = $em->getRepository(TeamMember::class)->findByUser($user);

        return [
            'user' => $user,
            'teamsMember' => $teamsMember,
            'form' => $form->createView(),
            'formNote' => $formNote->createView()
        ];
    }

    /**
     * @Route("/{type}/remove/{user}")
     */
    public function removeImageAction($type = 'avatar', User $user)
    {
        if ('avatar' === $type) {
            $user->setAvatar(User::DEFAULT_AVATAR);
        } else {
            $user->setBanner(User::DEFAULT_BANNER);
        }

        $this->addFlash('success', ucfirst($type).' removed successfully!');
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin_user_update', ['user' => $user->getId()]);
    }
}
