<?php

namespace TournamentBundle\Workflow;

/**
 * Class StreamWorkflow
 * @package TournamentBundle\Workflow
 */
final class StreamWorkflow
{
    // States
    const STATE_REQUESTED = 'requested';
    const STATE_ACCEPTED = 'accepted';
    const STATE_REFUSED = 'refused';

    // Trans
    const TRANS_ACCEPT = 'accept';
    const TRANS_REFUSE = 'refuse';
}
