<?php

namespace TournamentBundle\Twig;

use TournamentBundle\Entity\RankingLevel;

/**
 * Class RankingExtension
 * @package TournamentBundle\Twig
 */
class RankingExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('rankingLevelLogo', [$this, 'rankingLevelLogo']),
            new \Twig_SimpleFilter('rankingLevelColor', [$this, 'rankingLevelColor'], ['is_safe' => ['html' => true]])
        ];
    }

    /**
     * @param int   $elo
     * @param array $levels
     *
     * @return string
     */
    public function rankingLevelLogo(int $elo, array $levels)
    {
        /** @var RankingLevel $level */
        foreach ($levels as $level) {
            if ($level->getEloMax() < $elo) {
                continue;
            }

            return $level->getWebPath();
        }

        return '';
    }

    /**
     * @param int    $elo
     * @param array  $levels
     * @param string $locale
     *
     * @return string
     */
    public function rankingLevelColor(int $elo, array $levels, string $locale)
    {
        /** @var RankingLevel $level */
        foreach ($levels as $level) {
            if ($level->getEloMax() < $elo) {
                continue;
            }

            $method = 'getName'.ucfirst($locale);
            return '<span class=\'text-bold\' style=\'color:'.$level->getColor().'\';>'.$level->$method().'</span>';
        }

        return (new RankingLevel())->getColor();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ranking_extension';
    }
}
