<?php

namespace TournamentBundle\Repository;

use Doctrine\ORM\Query\Expr\Join;
use TournamentBundle\Entity\Ranking;
use UserBundle\Entity\Team;
use UserBundle\Entity\User;

/**
 * RankingParticipantRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RankingParticipantRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param User $user
     *
     * @return mixed
     */
    public function getRankingParticipantsForUser(User $user)
    {
        return $this->createQueryBuilder('rp')
            ->join(User::class, 'u', Join::WITH, 'rp.user = u.id')
            ->where('rp.user = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param Team $team
     *
     * @return mixed
     */
    public function getRankingParticipantsForTeam(Team $team)
    {
        return $this->createQueryBuilder('rp')
            ->join(Team::class, 't', Join::WITH, 'rp.team = t.id')
            ->where('rp.team = :teamId')
            ->setParameter('teamId', $team->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return mixed
     */
    public function getTopYearRanking()
    {
        return $this->createQueryBuilder('rp')
            ->join(Ranking::class, 'r', Join::WITH, 'rp.ranking = r.id')
            ->where('r.seasonNumber IS NULL')
            ->orderBy('rp.elo', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
}
