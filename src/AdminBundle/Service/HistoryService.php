<?php

namespace AdminBundle\Service;

use AdminBundle\Entity\History;
use Doctrine\ORM\EntityManagerInterface;
use UserBundle\Entity\User;

/**
 * Class HistoryService
 * @package AdminBundle\Service
 */
class HistoryService
{
    /** @var EntityManagerInterface */
    protected $manager;

    /**
     * HistoryService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->manager = $entityManager;
    }

    /**
     * @param int    $entityId
     * @param string $entityName
     * @param string $content
     * @param User   $user
     *
     * @return bool
     */
    public function add(int $entityId, string $entityName, string $content, User $user)
    {
        $history = new History();

        $history
            ->setEntityId($entityId)
            ->setEntityName($entityName)
            ->setContent($content)
            ->setUser($user)
        ;

        $this->manager->persist($history);
        $this->manager->flush();

        return true;
    }
}
