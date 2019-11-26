<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadFixtures
 * @package AppBundle\DataFixtures\ORM
 */
class LoadFixtures implements FixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        \Nelmio\Alice\Fixtures::load([
            __DIR__ . '/user.yml',
            __DIR__ . '/team.yml',
            __DIR__ . '/team_member.yml',
            __DIR__ . '/game.yml',
            __DIR__ . '/tag.yml',
            __DIR__ . '/mode.yml',
            __DIR__ . '/description.yml',
            __DIR__ . '/organizer.yml',
            __DIR__ . '/reward.yml',
            __DIR__ . '/rules.yml',
            __DIR__ . '/tournament.yml',
            __DIR__ . '/participant.yml',
            __DIR__ . '/notification_template.yml',
            __DIR__ . '/notification.yml',
            __DIR__ . '/mail.yml',
            __DIR__ . '/lan.yml',
            __DIR__ . '/lan_participant.yml',
            __DIR__ . '/battle.yml',
            __DIR__ . '/ranking_level.yml',
            __DIR__ . '/ranking.yml',
            __DIR__ . '/news.yml'
        ], $manager, ['providers' => [$this]]);
    }
}
