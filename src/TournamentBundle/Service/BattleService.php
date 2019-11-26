<?php

namespace TournamentBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use TournamentBundle\Entity\Battle;
use TournamentBundle\Entity\Participant;
use TournamentBundle\Entity\Tournament;

/**
 * Class BattleService
 * @package TournamentBundle\Service
 */
class BattleService
{
    /** @var EntityManager */
    protected $manager;

    /** @var FlashBagInterface */
    protected $flashbag;

    /**
     * BattleService constructor.
     *
     * @param EntityManager     $manager
     * @param FlashBagInterface $flashBag
     */
    public function __construct(EntityManager $manager, FlashBagInterface $flashBag)
    {
        $this->manager = $manager;
        $this->flashbag = $flashBag;
    }

    /**
     * @param Tournament       $tournament
     * @param Participant|null $participantUserOrTeam1
     * @param Participant|null $participantUserOrTeam2
     * @param int              $battleId
     * @param int|null         $round
     */
    public function add(
        Tournament $tournament,
        Participant $participantUserOrTeam1 = null,
        Participant $participantUserOrTeam2 = null,
        int $battleId,
        int $round = null
    ) {
        $battle = new Battle();

        $battle
            ->setParticipant1($participantUserOrTeam1)
            ->setParticipant2($participantUserOrTeam2)
            ->setBattleId($battleId)
            ->setRound($round ?? 1)
            ->setTournament($tournament)
        ;

        try {
            $this->manager->persist($battle);
        } catch (ORMException $e) {
            return;
        }
    }

    /**
     * @param Battle      $battle
     * @param Participant $winner
     */
    public function win(Battle $battle, Participant $winner)
    {
        if ($winner !== $battle->getParticipant1() && $winner !== $battle->getParticipant2()) {
            return;
        }

        $battle
            ->setScore1($battle->getParticipant1() === $winner ? $battle->getScore1() + 1 : $battle->getScore1())
            ->setScore2($battle->getParticipant2() === $winner ? $battle->getScore2() + 1 : $battle->getScore2())
            ->setEndAt(new \DateTime())
        ;

        $this->addOrUpdate($battle);

        try {
            $this->manager->flush();
        } catch (OptimisticLockException $e) {
            return;
        } catch (ORMException $e) {
            return;
        }
    }

    /**
     * @param Battle $battle
     */
    public function reset(Battle $battle)
    {
        $tournament = $battle->getTournament();
        $nextBattle = $this->manager->getRepository(Battle::class)->findOneBy(['tournament' => $tournament, 'battleId' => $this->getNextBattleId($battle)]);

        if ($nextBattle && $nextBattle->getEndAt()) {
            $this->flashbag->add('danger', 'Impossible, the next match it\'s already finished, try to reset the next match first');

            return;
        }

        if ($nextBattle) {
            $this->resetData($nextBattle, $battle);

            // Consolation battle
            if (intval(log($tournament->getSize(), 2)) - 1 === $battle->getRound()) {
                $otherNextBattle = $this->manager->getRepository(Battle::class)->findOneBy(['tournament' => $tournament, 'battleId' => $this->getNextBattleId($battle) + 1]);
                if (null !== $otherNextBattle && null === $otherNextBattle->getEndAt()) {
                    $this->resetData($otherNextBattle, $battle);
                }
            }
        }

        $this->resetData($battle);
    }

    /**
     * @param Battle $battle
     * @param Battle $previousBattle
     */
    private function resetData(Battle $battle, Battle $previousBattle = null)
    {
        $battle
            ->setScore1(0)
            ->setScore2(0)
            ->setStartAt(null === $previousBattle ? new \DateTime() : null)
            ->setEndAt(null)
            ->setContested(false)
            ->setNote(null)
        ;

        if (null === $previousBattle) {
            return;
        }

        $battle
            ->setParticipant1($battle->getParticipant1() === $previousBattle->getParticipant1() || $battle->getParticipant1() === $previousBattle->getParticipant2() ? null : $battle->getParticipant1())
            ->setParticipant2($battle->getParticipant2() === $previousBattle->getParticipant2() || $battle->getParticipant2() === $previousBattle->getParticipant1() ? null : $battle->getParticipant2())
        ;

        if (null === $battle->getParticipant1() && null === $battle->getParticipant2()) {
            try {
                $this->manager->remove($battle);
            } catch (ORMException $e) {
                return;
            }
        }
    }

    /**
     * @param Battle $battle
     */
    private function addOrUpdate(Battle $battle)
    {
        $nextBattleId = $this->getNextBattleId($battle);
        $winner = $battle->getWinner();
        $loser = $battle->getLoser();
        $tournament = $battle->getTournament();

        /** @var Battle $nextBattle */
        $nextBattle = $this->manager->getRepository(Battle::class)->findOneBy(
            ['tournament' => $tournament, 'battleId' => $nextBattleId]
        );

        // If not nextBattle, create next match (and consolation battle if final)
        if ($nextBattleId && null === $nextBattle) {
            $this->add(
                $tournament,
                1 === $battle->getBattleId() % 2 ? $winner : null,
                0 === $battle->getBattleId() % 2 ? $winner : null,
                $nextBattleId,
                $battle->getRound() + 1
            );

            // Consolation battle
            if (intval(log($tournament->getSize(), 2)) - 1 === $battle->getRound()) {
                $this->add(
                    $tournament,
                    1 === $battle->getBattleId() % 2 ? $loser : null,
                    0 === $battle->getBattleId() % 2 ? $loser : null,
                    $nextBattleId + 1,
                    $battle->getRound() + 1
                );
            }

            return;
        }

        if ($nextBattle) {
            $nextBattle
                ->setParticipant1($nextBattle->getParticipant1() ?? $winner)
                ->setParticipant2($nextBattle->getParticipant2() ?? $winner)
                ->setStartAt(new \DateTime())
            ;
        }

        if (intval(log($tournament->getSize(), 2)) - 1 === $battle->getRound()) {
            /** @var Battle $consolationBattle */
            $consolationBattle = $this->manager->getRepository(Battle::class)->findOneBy(
                ['tournament' => $tournament, 'battleId' => $tournament->getSize()]
            );

            if (null !== $consolationBattle) {
                $consolationBattle
                    ->setParticipant1($consolationBattle->getParticipant1() ?? $loser)
                    ->setParticipant2($consolationBattle->getParticipant2() ?? $loser)
                    ->setStartAt(new \DateTime())
                ;
            }
        }
    }

    /**
     * @param Tournament $tournament
     * @param array      $participants
     *
     * @return array
     */
    public function getBattlesForTournamentStart(Tournament $tournament, array $participants)
    {
        $participantsWithBye = $this->getParticipantsWithBye($tournament, $participants);

        $battles = [];
        $battleId = 1;
        $i = 1;
        foreach ($participantsWithBye as $participant) {
            if (!isset($battles[$battleId])) {
                $battles[$battleId] = [];
            }

            $battles[$battleId][] = $participant;

            if ($i % 2 === 0) {
                ++$battleId;
            }

            ++$i;
        }

        return $battles;
    }

    /**
     * @param Battle $battle
     *
     * @return int|null
     */
    private function getNextBattleId(Battle $battle)
    {
        if ($battle->getBattleId() === $battle->getTournament()->getSize() ||
            $battle->getBattleId() === $battle->getTournament()->getSize() - 1
        ) {
            return null;
        }

        $nextBattleId = intval($battle->getTournament()->getSize() / 2) + round(($battle->getBattleId() / 2), 0, PHP_ROUND_HALF_UP);

        return $nextBattleId;
    }

    /**
     * @param Tournament $tournament
     * @param array      $participants
     *
     * @return array
     */
    private function getParticipantsWithBye(Tournament $tournament, array $participants)
    {
        $participantsWithBye = [];
        $numberOfByeRemaining = $tournament->getSize() - count($participants);
        $t = 0;
        for ($i = 0 ; $i < $tournament->getSize() ; ++$i) {
            if ($i % 2 !== 0 && $numberOfByeRemaining > 0) {
                $participantsWithBye[] = null;
                --$numberOfByeRemaining;

                continue;
            }

            $participantsWithBye[] = $participants[$t];
            ++$t;
        }

        return $participantsWithBye;
    }
}
