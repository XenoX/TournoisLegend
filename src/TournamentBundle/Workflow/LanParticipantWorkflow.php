<?php

namespace TournamentBundle\Workflow;

/**
 * Class LanParticipantWorkflow
 * @package TournamentBundle\Workflow
 */
final class LanParticipantWorkflow
{
    // States
    const STATE_REGISTERED = 'registered';
    const STATE_CONFIRMED = 'confirmed';

    // Trans
    const TRANS_CONFIRM = 'confirm';
}
