<?php

namespace UserBundle\Service;

use Cocur\Slugify\SlugifyInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RouterInterface;
use TournamentBundle\Entity\Lan;
use TournamentBundle\Entity\Tournament;
use UserBundle\Entity\Notification;
use UserBundle\Entity\NotificationTemplate;
use UserBundle\Entity\Team;
use UserBundle\Entity\User;

class NotificationService
{
    /** @var EntityManager */
    protected $manager;

    /** @var SlugifyInterface */
    protected $slugify;

    /** @var RouterInterface */
    protected $router;

    /** @var \Twig_Environment */
    protected $twig;

    /**
     * @param EntityManager     $manager
     * @param SlugifyInterface  $slugify
     * @param RouterInterface   $router
     * @param \Twig_Environment $twig
     */
    public function __construct(EntityManager $manager, SlugifyInterface $slugify, RouterInterface $router, \Twig_Environment $twig)
    {
        $this->manager = $manager;
        $this->slugify = $slugify;
        $this->router = $router;
        $this->twig = $twig;
    }

    /**
     * @param NotificationTemplate $template
     * @param string               $type
     * @param string|null          $value
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addAdmin(NotificationTemplate $template, string $type, string $value = null)
    {
        $lastConnectedUsers = $this->manager->getRepository(User::class)->findBy([], ['lastLoginAt' => 'DESC'], 10000);

        $i = 1;
        foreach ($lastConnectedUsers as $user) {
            $notification = new Notification();

            $notification
                ->setUser($user)
                ->setType($type)
                ->setTemplate($template)
                ->setValue($value)
            ;

            $this->manager->persist($notification);

            if (($i % 1000) === 0) {
                $this->manager->flush();
            }

            ++$i;
        }

        $this->manager->flush();
    }

    /**
     * @param User   $user
     * @param string $type
     * @param string $name
     * @param int    $value
     */
    public function add(User $user, string $type, string $name, int $value = null)
    {
        $template = $this->manager->getRepository(NotificationTemplate::class)->findOneBy(['name' => $name]);

        if (null === $template) {
            return;
        }

        $notification = new Notification();
        $notification
            ->setUser($user)
            ->setType($type)
            ->setTemplate($template)
            ->setValue($value)
        ;

        $this->manager->persist($notification);
    }

    /**
     * @param array $notifications
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function getHtml(array $notifications)
    {
        $notificationsNonSeenCount = 0;
        /** @var Notification $notification */
        foreach ($notifications as $notification) {
            if (null !== $notification->getSeenAt()) {
                continue;
            }

            ++$notificationsNonSeenCount;
        }

        return $this->twig->render(
            '@User/Include/notifications.html.twig',
            [
                'notifications' => $notifications,
                'links' => $this->getLinks($notifications),
                'notificationsNonSeenCount' => $notificationsNonSeenCount
            ]
        );
    }

    /**
     * @param array $notifications
     *
     * @return array
     */
    private function getLinks(array $notifications)
    {
        $links = [];
        /** @var Notification $notification */
        foreach ($notifications as $notification) {
            if (null === $notification->getValue()) {
                $links[$notification->getId()] = '';
                continue;
            }

            $links[$notification->getId()] = null;
            switch ($notification->getType()) {
                case 'user':
                    $user = $this->manager->getRepository(User::class)->find($notification->getValue());
                    if ($user) {
                        $links[$notification->getId()] = $this->router->generate('user_user_profile', ['user' => $user->getId(), 'slug' => $this->slugify->slugify($user->getName())]);
                    }
                    break;
                case 'team':
                    $team = $this->manager->getRepository(Team::class)->find($notification->getValue());
                    if ($team) {
                        $links[$notification->getId()] = $this->router->generate('user_team_profile', ['team' => $team->getId(), 'slug' => $this->slugify->slugify($team->getName())]);
                    }
                    break;
                case 'tournament':
                    $tournament = $this->manager->getRepository(Tournament::class)->find($notification->getValue());
                    if ($tournament) {
                        $links[$notification->getId()] = $this->router->generate('tournament_tournament_profile', ['game' => $this->slugify->slugify($tournament->getMode()->getGame()->getName()), 'tournament' => $tournament->getId(), 'slug' => $this->slugify->slugify($tournament->getName())]);
                    }
                    break;
                case 'lan':
                    $lan = $this->manager->getRepository(Lan::class)->find($notification->getValue());
                    if ($lan) {
                        $links[$notification->getId()] = $this->router->generate('tournament_lan_view', ['lan' => $lan->getId(), 'slug' => $this->slugify->slugify($lan->getName())]);
                    }
                    break;
                case 'welcome':
                    $links[$notification->getId()] = '';
                    break;
                default:
                    $links[$notification->getId()] = '';
                    break;
            }
        }

        return $links;
    }
}
