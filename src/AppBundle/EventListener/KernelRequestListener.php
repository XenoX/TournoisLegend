<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class KernelRequestListener
 * @package AppBundle\EventListener
 */
class KernelRequestListener
{
    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if ($locale !== 'fr') $locale = 'en';

        $request->setLocale($locale);
    }
}
