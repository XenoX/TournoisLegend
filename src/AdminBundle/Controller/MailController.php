<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use TournamentBundle\Entity\Mail;
use AdminBundle\Form\MailType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/tournament/mail")
 */
class MailController extends Controller
{
    /**
     * @Route("/update/{mail}")
     * @Route("/create", name="admin_mail_create")
     * @Template
     */
    public function updateAction(Request $request, Mail $mail = null)
    {
        $mail ?: $mail = new Mail();

        $form = $this->createForm(MailType::class, $mail);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($mail);
            $em->flush();

            $this->addFlash('success', 'Mail add or updated successfully!');

            return $this->redirect($this->generateUrl('admin_admin_lan'));
        }

        return array('form' => $form->createView(), 'mail' => $mail);
    }
}
