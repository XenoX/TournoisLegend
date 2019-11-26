<?php

namespace TournamentBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Workflow\Workflow;
use TournamentBundle\Entity\Organizer;
use TournamentBundle\Entity\Stream;
use TournamentBundle\Entity\Tournament;
use UserBundle\Entity\User;

/**
 * Class StreamService
 * @package TournamentBundle\Service
 */
class StreamService
{
    /** @var EntityManager */
    protected $manager;

    /** @var ValidatorInterface */
    protected $validator;

    /** @var Workflow */
    protected $workflow;

    /**
     * StreamService constructor.
     *
     * @param EntityManager      $manager
     * @param ValidatorInterface $validator
     * @param Workflow           $workflow
     */
    public function __construct(EntityManager $manager, ValidatorInterface $validator, Workflow $workflow)
    {
        $this->manager = $manager;
        $this->validator = $validator;
        $this->workflow = $workflow;
    }

    /**
     * @param string         $channel
     * @param Tournament     $tournament
     * @param User|null      $user
     * @param Organizer|null $organizer
     *
     * @return bool
     */
    public function add(string $channel, Tournament $tournament, User $user = null, Organizer $organizer = null)
    {
        $streamRequest = new Stream();
        $streamRequest
            ->setChannel($channel)
            ->setTournament($tournament)
            ->setUser($user)
            ->setOrganizer($organizer)
        ;

        if (false === $this->validate($streamRequest)) {
            return false;
        }

        $this->manager->persist($streamRequest);

        return true;
    }

    /**
     * @param Stream $stream
     *
     * @return bool
     */
    public function validate(Stream $stream)
    {
        if (null === $stream->getUser() && null === $stream->getOrganizer()) {
            return false;
        }

        if (preg_match('/\s/', $stream->getChannel())) {
            return false;
        }

        if (preg_match('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $stream->getChannel())) {
            return false;
        }

        $errors = $this->validator->validate($stream);

        if (0 < count($errors)) {
            return false;
        }

        $alreadyRegistered = $this->manager->getRepository(Stream::class)->findOneBy([
            'user' => $stream->getUser(),
            'tournament' => $stream->getTournament()
        ]);

        if (null !== $alreadyRegistered) {
            return false;
        }

        return true;
    }
}
