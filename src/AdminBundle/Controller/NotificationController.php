<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\NotificationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/notification/")
 */
class NotificationController extends Controller
{
    /**
     * @Route("/create")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(NotificationType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('user.notification')->addAdmin(
                $form->get('template')->getData(), $form->get('type')->getData(), $form->get('value')->getData()
            );

            $this->addFlash('success', 'Notification add successfully!');

            return $this->redirectToRoute('admin_admin_data');
        }

        return ['form' => $form->createView()];
    }
}
