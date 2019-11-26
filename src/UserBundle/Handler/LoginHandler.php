<?php

namespace UserBundle\Handler;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use UserBundle\Entity\User;

class LoginHandler implements AuthenticationSuccessHandlerInterface
{
    /** @var EntityManager */
    private $entityManager;

    /** @var RouterInterface */
    private $router;

    /**
     * LoginHandler constructor.
     *
     * @param EntityManager   $entityManager
     * @param RouterInterface $router
     */
    public function __construct(EntityManager $entityManager, RouterInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        $user
            ->setLastLoginAt(new \DateTime())
            ->setLocale($request->getLocale())
        ;

        $this->entityManager->flush();

        return new RedirectResponse($this->getBetterTarget($request));
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getBetterTarget(Request $request)
    {
        $firewallReferer = $request->getSession()->get('_security.main.target_path');
        $userReferer = $request->getSession()->get('user_referer');
        $referer = $this->router->generate('app_app_index');

        if ($firewallReferer) {
            return $firewallReferer;
        } elseif ($userReferer) {
            return $userReferer;
        }

        return $referer;
    }
}
