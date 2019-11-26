<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Game;
use Doctrine\ORM\Query\Expr\Join;

/**
 * ModeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ModeRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     */
    public function getPlayedGames()
    {
        $qb = $this->createQueryBuilder('m');

        $query = $qb
            ->select('g')
            ->join(Game::class, 'g', Join::WITH, 'm.game = g.id')
            ->where('m.activated = :activated')
            ->setParameter('activated', true)
            ->groupBy('g.id')
            ->getQuery()
        ;

        return $query->getResult();
    }
}
