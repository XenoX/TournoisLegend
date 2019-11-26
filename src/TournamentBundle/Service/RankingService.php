<?php

namespace TournamentBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use TournamentBundle\Entity\Battle;
use TournamentBundle\Entity\Participant;
use TournamentBundle\Entity\Ranking;
use TournamentBundle\Entity\RankingParticipant;
use TournamentBundle\Entity\Tournament;
use UserBundle\Entity\Team;
use UserBundle\Entity\User;

/**
 * Class RankingService
 * @package AppBundle\Service
 */
class RankingService
{
    /** @var ParticipantService */
    private $participantService;

    /** @var EntityManagerInterface */
    private $manager;

    /**
     * RankingService constructor.
     *
     * @param ParticipantService     $participantService
     * @param EntityManagerInterface $manager
     */
    public function __construct(ParticipantService $participantService, EntityManagerInterface $manager)
    {
        $this->participantService = $participantService;
        $this->manager = $manager;
    }

    /**
     * @param Tournament $tournament
     */
    public function update(Tournament $tournament)
    {
        $yearRanking = $this->getYearRanking($tournament);
        $seasonRanking = $this->getSeasonRanking($tournament);

        if (null === $yearRanking || null === $seasonRanking) {
            return;
        }

        $participants = $this->participantService->getTournamentParticipants($tournament);

        $battles = $this->manager->getRepository(Battle::class)->findBy(
            ['tournament' => $tournament], ['battleId' => 'ASC']
        );

        $this->updateRanking($participants, $yearRanking, $tournament, $battles);
        $this->updateRanking($participants, $seasonRanking, $tournament, $battles);
    }

    /**
     * @param array      $participants
     * @param Ranking    $ranking
     * @param Tournament $tournament
     * @param Battle[]   $battles
     */
    private function updateRanking(array $participants, Ranking $ranking, Tournament $tournament, array $battles)
    {
        $rankingParticipants = $this->getRankingParticipants($participants, $ranking, $tournament);
        $tournamentSize = $tournament->getSize();

        foreach ($battles as $battle) {
            $battleId = $battle->getBattleId();

            /** @var RankingParticipant $winnerRankingParticipant */
            $winnerRankingParticipant = $rankingParticipants[$battle->getWinner()->getTeamOrUser()->getId()];
            if ($battle->getLoser()) {
                /** @var RankingParticipant $loserRankingParticipant */
                $loserRankingParticipant = $rankingParticipants[$battle->getLoser()->getTeamOrUser()->getId()];
            }

            $winnerElo = $winnerRankingParticipant->getElo();
            $loserElo = isset($loserRankingParticipant) ? $loserRankingParticipant->getElo() : Ranking::BASE_ELO;
            $rankingRatio = $tournament->getRankingRatio();

            $points = $this->getPoints($rankingRatio, $battle->getRound(), $winnerElo, $loserElo);

            $winnerTotalElo = $winnerElo + $points[0] + 10 + $points[1];
            $loserTotalElo = $loserElo - ($points[0] + $points[1]);
            if ($battleId === $tournamentSize || $battleId === $tournamentSize - 1) {
                $loserTotalElo = $loserElo + (($points[0] - 10) - $points[1]);
            }

            $winnerRankingParticipant->setElo($winnerTotalElo > 0 ? $winnerTotalElo : 0);
            if (isset($loserRankingParticipant)) {
                $loserRankingParticipant->setElo($loserTotalElo > 0 ? $loserTotalElo : 0);
            }
        }
    }

    /**
     * @param float $rankingRatio
     * @param int   $round
     * @param int   $eloWinner
     * @param int   $eloLoser
     *
     * @return array
     */
    private function getPoints(float $rankingRatio, int $round, int $eloWinner, int $eloLoser)
    {
        $pointsByDiff = [[0, 100], [101, 200], [201, 300], [301, 500], [501, 9999]];

        $basicPoints = ($round * 10) * $rankingRatio;
        $eloDiff = abs($eloWinner - $eloLoser);

        $bonusPoints = 0;
        foreach ($pointsByDiff as $key => $points) {
            if ($points[0] <= $eloDiff && $points[1] >= $eloDiff) {
                $bonusPoints = $key * 2;
                break;
            }
        }

        if ($eloWinner > $eloLoser) {
            $bonusPoints = $bonusPoints * -1;
        }

        return [$basicPoints, $bonusPoints];
    }

    /**
     * @param array      $participants
     * @param Ranking    $ranking
     * @param Tournament $tournament
     *
     * @return array
     */
    private function getRankingParticipants(array $participants, Ranking $ranking, Tournament $tournament)
    {
        $teamOrUserField = $tournament->isTeam() ? 'team' : 'user';
        $teamsOrUsers = [];

        /** @var Participant[] $participant */
        foreach ($participants as $participant) {
            /** @var Participant $participant */
            $participant = $participant[0];
            $teamOrUser = $participant->getUser();

            if ($tournament->isTeam()) {
                $teamOrUser = $participant->getTeam();
            }

            if (isset($teamsOrUsers[$teamOrUser->getId()])) {
                continue;
            }

            $teamsOrUsers[$teamOrUser->getId()] = $teamOrUser;
        }

        $rankingParticipants = [];
        /** @var Team|User $teamOrUser */
        foreach ($teamsOrUsers as $teamOrUser) {
            $rankingParticipant = $this->manager->getRepository(RankingParticipant::class)->findOneBy(
                ['ranking' => $ranking, $teamOrUserField => $teamOrUser]
            );

            if (null === $rankingParticipant) {
                $rankingParticipant = new RankingParticipant();
                $rankingParticipant
                    ->setElo($ranking->getBaseElo())
                    ->setRanking($ranking)
                    ->setTeam($teamOrUser instanceof Team ? $teamOrUser : null)
                    ->setUser($teamOrUser instanceof User ? $teamOrUser : null)
                ;

                $this->manager->persist($rankingParticipant);
            }

            $rankingParticipants[$teamOrUser->getId()] = $rankingParticipant;
        }

        return $rankingParticipants;
    }

    /**
     * @param Tournament $tournament
     *
     * @return Ranking $ranking
     */
    private function getSeasonRanking(Tournament $tournament)
    {
        $seasonPeriod = $this->getSeasonPeriod($tournament->getStartAt());

        $ranking = $this->manager->getRepository(Ranking::class)->findOneBy(
            ['mode' => $tournament->getMode(),
            'startAt' => new \DateTime($seasonPeriod[0]),
            'stopAt' => new \DateTime($seasonPeriod[1])]
        );

        if (null === $ranking) {
            $ranking = $this->createSeasonRanking($tournament, $seasonPeriod);
        }

        return $ranking;
    }

    /**
     * @param Tournament $tournament
     *
     * @return Ranking
     */
    private function getYearRanking(Tournament $tournament)
    {
        $year = $tournament->getStartAt()->format('Y');

        $ranking = $this->manager->getRepository(Ranking::class)->findOneBy(
            ['mode' => $tournament->getMode(), 'nameEn' => $year]
        );

        if (null === $ranking) {
            $ranking = $this->createYearRanking($tournament, $year);
        }

        return $ranking;
    }

    /**
     * @param Tournament $tournament
     * @param int        $year
     *
     * @return Ranking
     */
    private function createYearRanking(Tournament $tournament, int $year)
    {
        $ranking = new Ranking();
        $ranking
            ->setMode($tournament->getMode())
            ->setNameEn($year)
            ->setNameFr($year)
            ->setStartAt(new \DateTime($year.'-01-01'))
            ->setStopAt(new \DateTime($year.'-12-31'))
        ;

        $this->manager->persist($ranking);

        return $ranking;
    }

    /**
     * @param Tournament $tournament
     * @param array      $seasonPeriod
     *
     * @return Ranking
     */
    private function createSeasonRanking(Tournament $tournament, array $seasonPeriod)
    {
        $lastSeasonRanking = $this->manager->getRepository(Ranking::class)->findOneBy(
            ['mode' => $tournament->getMode()], ['seasonNumber' => 'DESC']
        );

        $seasonNumber = 1;
        if (null !== $lastSeasonRanking) {
            $seasonNumber = $lastSeasonRanking->getSeasonNumber();
        }

        $ranking = new Ranking();
        $ranking
            ->setMode($tournament->getMode())
            ->setNameEn('Season '.$seasonNumber)
            ->setNameFr('Saison '.$seasonNumber)
            ->setSeasonNumber($seasonNumber)
            ->setStartAt(new \DateTime($seasonPeriod[0]))
            ->setStopAt(new \DateTime($seasonPeriod[1]))
        ;

        $this->manager->persist($ranking);

        return $ranking;
    }

    /**
     * @param \DateTime $startAt
     *
     * @return array
     */
    private function getSeasonPeriod(\DateTime $startAt)
    {
        $month = $startAt->format('m');
        $year = $startAt->format('Y');
        $seasons = [[1, 3], [4, 6], [7, 9], [10, 12]];
        $seasonNumber = 0;

        foreach ($seasons as $key => $season) {
            if ($month >= $season[0] && $month <= $season[1]) {
                $seasonNumber = $key;
                break;
            }
        }

        $periods = [
            [$year.'-01-01', $year.'-03-31'],
            [$year.'-04-01', $year.'-06-30'],
            [$year.'-07-01', $year.'-09-30'],
            [$year.'-10-01', $year.'-12-31']
        ];

        return $periods[$seasonNumber];
    }
}
