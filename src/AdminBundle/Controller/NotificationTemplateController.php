<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\NotificationTemplateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\NotificationTemplate;

/**
 * @Route("/notification/template")
 */
class NotificationTemplateController extends Controller
{
    /**
     * @Route("/update/{template}")
     * @Route("/create", name="admin_notificationtemplate_create")
     * @Template()
     */
    public function updateAction(Request $request, NotificationTemplate $template = null)
    {
        $template ?: $template = new NotificationTemplate();

        $form = $this->createForm(NotificationTemplateType::class, $template);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($template);
            $em->flush();

            $this->addFlash('success', 'Template add or updated successfully!');

            return $this->redirectToRoute('admin_admin_data');
        }

        return ['template' => $template, 'form' => $form->createView()];
    }
}
