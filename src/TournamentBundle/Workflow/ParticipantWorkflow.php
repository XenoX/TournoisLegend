<?php

namespace TournamentBundle\Workflow;

/**
 * Class ParticipantWorkflow
 * @package TournamentBundle\Workflow
 */
final class ParticipantWorkflow
{
    // States
    const STATE_REGISTERED = 'registered';
    const STATE_CONFIRMED = 'confirmed';
    const STATE_CHECKED_IN = 'checked_in';

    // Trans
    const TRANS_CONFIRM = 'confirm';
    const TRANS_CHECK_IN = 'check_in';
}
