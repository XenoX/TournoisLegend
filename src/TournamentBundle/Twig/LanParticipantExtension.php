<?php

namespace TournamentBundle\Twig;

use Symfony\Component\Translation\TranslatorInterface;
use TournamentBundle\Entity\LanParticipant;
use TournamentBundle\Workflow\LanParticipantWorkflow;

class LanParticipantExtension extends \Twig_Extension
{
    /** @var TranslatorInterface */
    private $translator;

    /**
     * ParticipantExtension constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('lanParticipantState', [$this, 'getLanParticipantState'], ['is_safe' => ['html' => true]])];
    }

    /**
     * @param LanParticipant $participant
     *
     * @return string
     */
    public function getLanParticipantState(LanParticipant $participant)
    {
        $states = [
            LanParticipantWorkflow::STATE_REGISTERED => ['participant.state.registered', 'text-warning'],
            LanParticipantWorkflow::STATE_CONFIRMED => ['participant.state.confirmed', 'text-primary']
        ];

        $state = $participant->getState();

        if (!isset($states[$state])) {
            $states[$state] = ['tournament.state.unknow', 'text-danger'];
        }

        return '<span class=\''.$states[$state][1].'\'>'.$this->translator->trans($states[$state][0]).'</span>';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lan_participant_extension';
    }
}
