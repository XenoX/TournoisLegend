<?php

namespace TournamentBundle\Repository;

use AppBundle\Entity\Game;
use Doctrine\ORM\NonUniqueResultException;
use TournamentBundle\Workflow\TournamentWorkflow;

/**
 * TournamentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TournamentRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param string    $state
     * @param Game|null $game
     *
     * @return array
     */
    public function findByStateAndGameTournaments(string $state, Game $game = null)
    {
        $qb = $this->createQueryBuilder('t');

        if ($game) {
            $qb
                ->join('t.mode', 'mode')
                ->where('mode.game = :game')
                ->setParameter('game', $game)
            ;
        }

        $qb
            ->andWhere('t.activated = :activated')
            ->andWhere('t.state = :state')
            ->setParameter('state', $state)
            ->setParameter('activated', true)
            ->orderBy('t.startAt', 'ASC')
        ;

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @param Game|null $game
     * @param int|null  $limit
     *
     * @return array|mixed
     */
    public function findTournaments(Game $game = null, int $limit = null)
    {
        $qb = $this->createQueryBuilder('t');

        if ($game) {
            $qb
                ->join('t.mode', 'mode')
                ->where('mode.game = :game')
                ->setParameter('game', $game)
            ;
        }

        $qb
            ->andWhere(
                $qb->expr()->lte('t.startAt', ':nextWeek')
            )
            ->andWhere($qb->expr()->orX()
                ->add($qb->expr()->eq('t.state', ':init'))
                ->add($qb->expr()->eq('t.state', ':registration'))
                ->add($qb->expr()->eq('t.state', ':check_in'))
            )
            ->andWhere('t.activated = :activated')
            ->setParameter('nextWeek', new \DateTime('+15 day'))
            ->setParameter('init', TournamentWorkflow::STATE_INIT)
            ->setParameter('registration', TournamentWorkflow::STATE_REGISTRATION)
            ->setParameter('check_in', TournamentWorkflow::STATE_CHECK_IN)
            ->setParameter('activated', true)
            ->orderBy('t.startAt', 'ASC')
            ->setMaxResults($limit)
        ;

        $query = $qb->getQuery();

        if (1 === $limit) {
            try {
                return $query->getOneOrNullResult();
            } catch (NonUniqueResultException $e) {
                return null;
            }
        }

        return $query->getResult();
    }

    /**
     * @param Game|null $game
     * @param int       $limit
     *
     * @return array|mixed
     */
    public function findLastTournaments(Game $game = null, int $limit)
    {
        $qb = $this->createQueryBuilder('t');

        if ($game) {
            $qb
                ->join('t.mode', 'mode')
                ->where('mode.game = :game')
                ->setParameter('game', $game)
            ;
        }

        $qb
            ->andWhere('t.state = :done')
            ->andWhere('t.activated = :activated')
            ->setParameter('done', TournamentWorkflow::STATE_DONE)
            ->setParameter('activated', true)
            ->orderBy('t.startAt', 'DESC')
            ->setMaxResults($limit)
        ;

        $query = $qb->getQuery();

        if (1 === $limit) {
            try {
                return $query->getOneOrNullResult();
            } catch (NonUniqueResultException $e) {
                return null;
            }
        }

        return $query->getResult();
    }

    /**
     * @param string $keyword
     * @param bool   $findAll
     *
     * @return array
     */
    public function findByName(string $keyword, bool $findAll = false)
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.name LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%')
        ;

        if (false === $findAll) {
            $qb
                ->andWhere('t.activated = :activated')->setParameter('activated', true)
                ->setMaxResults(8)
            ;
        }

        $qb->orderBy('t.startAt', 'DESC');

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param int $weekNumber
     *
     * @return array
     */
    public function findByWeekNumber(int $weekNumber)
    {
        return $this->createQueryBuilder('t')
            ->where('WEEK(t.startAt, 7) = :weekNumber')
            ->andWhere('YEAR(t.startAt) = :year')
            ->setParameter('weekNumber', $weekNumber)
            ->setParameter('year', (new \DateTime())->format('Y'))
            ->orderBy('t.startAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param string $keyword
     *
     * @return array
     */
    public function search(string $keyword)
    {
        return $this->createQueryBuilder('t')
            ->where('t.name LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%')
            ->orderBy('t.startAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}