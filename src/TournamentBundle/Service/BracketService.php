<?php

namespace TournamentBundle\Service;

use Doctrine\ORM\EntityManager;
use TournamentBundle\Entity\Battle;
use TournamentBundle\Entity\Tournament;

/**
 * Class BracketService
 * @package TournamentBundle\Service
 */
class BracketService
{
    /** @var EntityManager */
    protected $manager;

    /**
     * BattleService constructor.
     *
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Tournament $tournament
     *
     * @return array|null
     */
    public function getFirstRoundParticipantBattle(Tournament $tournament)
    {
        $battles = $this->manager->getRepository(Battle::class)->findBy(['tournament' => $tournament, 'round' => 1]);

        if (0 === count($battles)) {
            return null;
        }

        $participants = [];
        foreach ($battles as $battle) {
            $participants[] = [
               $battle->getParticipant1() ? $battle->getParticipant1()->getTeamOrUser()->getName() : null,
               $battle->getParticipant2() ? $battle->getParticipant2()->getTeamOrUser()->getName() : null
           ];
        }

        return $participants;
    }

    /**
     * @param Tournament $tournament
     *
     * @return array
     */
    public function getBattleResults(Tournament $tournament)
    {
        $battles = $this->manager->getRepository(Battle::class)->findBy(['tournament' => $tournament], ['battleId' => 'ASC']);

        $results = [];
        // IF SINGLE ELIMINATION

        /** @var Battle $battle */
        foreach ($battles as $battle) {
            if (!isset($results[$battle->getRound()])) {
                $results[$battle->getRound()] = [];
            }

            array_push($results[$battle->getRound()], [
                $battle->getParticipant1() ? $battle->getScore1() : null,
                $battle->getParticipant2() ? $battle->getScore2() : null
            ]);
        }

        return [array_values($results)];
    }
}
