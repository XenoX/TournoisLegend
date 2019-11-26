<?php

namespace TournamentBundle\Twig;

use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\TranslatorInterface;
use TournamentBundle\Entity\Battle;
use TournamentBundle\Entity\Participant;
use TournamentBundle\Workflow\ParticipantWorkflow;

class ParticipantExtension extends \Twig_Extension
{
    /** @var TranslatorInterface */
    private $translator;

    /** @var Router */
    private $router;

    /**
     * ParticipantExtension constructor.
     *
     * @param TranslatorInterface $translator
     * @param Router              $router
     */
    public function __construct(TranslatorInterface $translator, Router $router)
    {
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('participantState', [$this, 'getParticipantState'], ['is_safe' => ['html' => true]]),
            new \Twig_SimpleFilter('participantBattle', [$this, 'getParticipantBattle'], ['is_safe' => ['html' => true]])
        ];
    }

    /**
     * @param Participant[] $participants
     *
     * @return string
     */
    public function getParticipantState(array $participants)
    {
        $states = [
            ParticipantWorkflow::STATE_REGISTERED => ['participant.state.registered', 'text-warning'],
            ParticipantWorkflow::STATE_CONFIRMED => ['participant.state.confirmed', 'text-primary'],
            ParticipantWorkflow::STATE_CHECKED_IN => ['participant.state.checked_in', 'text-success']
        ];

        $state = $participants[0]->getState();

        if (ParticipantWorkflow::STATE_CHECKED_IN !== $state) {
            foreach ($participants as $participant) {
                if (ParticipantWorkflow::STATE_REGISTERED === $participant->getState()) {
                    $state = ParticipantWorkflow::STATE_REGISTERED;

                    break;
                }
            }
        }

        if (!isset($states[$state])) {
            $states[$state] = ['tournament.state.unknow', 'text-danger'];
        }

        return '<span class=\''.$states[$state][1].'\'>'.$this->translator->trans($states[$state][0]).'</span>';
    }

    /**
     * @param Battle $battle
     *
     * @return string
     */
    public function getParticipantBattle(Battle $battle)
    {
        return $this->getParticipantLink($battle->getParticipant1(), $battle).' - '.$this->getParticipantLink($battle->getParticipant2(), $battle);
    }

    /**
     * @param Participant|null $participantUserOrTeam
     * @param Battle           $battle
     *
     * @return string
     */
    private function getParticipantLink(Participant $participantUserOrTeam = null, Battle $battle)
    {
        if (null === $participantUserOrTeam) {
            if (1 === $battle->getRound()) {
                return 'BYE';
            }

            return 'No participant';
        }

        $participant = $participantUserOrTeam->getUser();
        $routeName = 'admin_user_update';
        $type = 'user';
        if ($participantUserOrTeam->getTeam()) {
            $participant = $participantUserOrTeam->getTeam();
            $routeName = 'admin_team_update';
            $type = 'team';
        }

        return '<a href="'.$this->router->generate($routeName, [$type => $participant->getId()]).'" class="'.$this->getColor($participantUserOrTeam, $battle).'" target="_blank">'.$participant->getName().'</a>';
    }

    /**
     * @param Participant $participant
     * @param Battle      $battle
     *
     * @return string
     */
    private function getColor(Participant $participant, Battle $battle)
    {
        $winner = $this->getWinner($battle);

        if (null === $winner) {
            return 'text-primary';
        }

        if ($winner->getId() === $participant->getId()) {
            return 'text-success';
        }

        return 'text-danger';
    }

    /**
     * @param Battle $battle
     *
     * @return Participant|null
     */
    private function getWinner(Battle $battle)
    {
        if ($battle->getScore1() > $battle->getScore2()) {
            return $battle->getParticipant1();
        }

        if ($battle->getScore2() > $battle->getScore1()) {
            return $battle->getParticipant2();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'participant_extension';
    }
}
