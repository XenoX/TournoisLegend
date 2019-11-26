<?php

namespace TournamentBundle\Twig;

use Symfony\Component\Translation\TranslatorInterface;
use TournamentBundle\Workflow\TournamentWorkflow;

/**
 * Class TournamentExtension
 * @package TournamentBundle\Twig
 */
class TournamentExtension extends \Twig_Extension
{
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('tournamentState', [$this, 'getTournamentState'], ['is_safe' => ['html' => true]])];
    }

    /**
     * @param string $tournamentState
     * @return string
     */
    public function getTournamentState(string $tournamentState)
    {
        $states = [
            TournamentWorkflow::STATE_INIT => ['tournament.state.init', 'text-default'],
            TournamentWorkflow::STATE_REGISTRATION => ['tournament.state.registration', 'text-success'],
            TournamentWorkflow::STATE_CHECK_IN => ['tournament.state.check_in', 'text-info'],
            TournamentWorkflow::STATE_MATCH => ['tournament.state.match', 'text-warning'],
            TournamentWorkflow::STATE_DONE => ['tournament.state.done', 'text-primary'],
        ];

        if (!isset($states[$tournamentState])) {
            $states[$tournamentState] = ['tournament.state.unknow', 'text-danger'];
        }

        return '<span class=\''.$states[$tournamentState][1].'\'>'.$this->translator->trans($states[$tournamentState][0]).'</span>';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tournament_state_extension';
    }
}
