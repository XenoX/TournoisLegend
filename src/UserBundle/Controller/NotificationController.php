<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\Notification;

class NotificationController extends Controller
{
    /**
     * @Route("/notifications/last", name="user_notification_last", condition="request.isXmlHttpRequest()")
     */
    public function getLastNotificationsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $notifications = $em->getRepository(Notification::class)->findBy(['user' => $user], ['id' => 'DESC'], 5);

        return new Response($this->get('user.notification')->getHtml($notifications));
    }

    /**
     * @Route("/notifications/open", name="user_notification_open", condition="request.isXmlHttpRequest()")
     */
    public function openLastNotificationsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $notifications = $em->getRepository(Notification::class)->findBy(['user' => $user, 'seenAt' => null]);

        /** @var Notification $notification */
        foreach ($notifications as $notification) {
            $notification->setSeenAt(new \DateTime());
        }

        $em->flush();

        return new Response(null);
    }
}
