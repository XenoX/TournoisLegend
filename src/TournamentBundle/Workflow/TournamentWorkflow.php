<?php

namespace TournamentBundle\Workflow;

/**
 * Class TournamentWorkflow
 * @package TournamentBundle\Workflow
 */
final class TournamentWorkflow
{
    // States
    const STATE_INIT = 'init';
    const STATE_REGISTRATION = 'registration';
    const STATE_CHECK_IN = 'check_in';
    const STATE_MATCH = 'match';
    const STATE_DONE = 'done';

    const STATES_BEFORE_CHECKIN = [self::STATE_INIT, self::STATE_REGISTRATION];
    const STATES_NOT_STARTED = [self::STATE_INIT, self::STATE_REGISTRATION, self::STATE_CHECK_IN];
    const STATES_IN_PROGRESS = [self::STATE_CHECK_IN, self::STATE_MATCH];
    const STATES_NOT_DONE = [self::STATE_INIT, self::STATE_REGISTRATION, self::STATE_CHECK_IN, self::STATE_MATCH];

    // Trans
    const TRANS_REGISTRATION = 'registration';
    const TRANS_CHECK_IN = 'check_in';
    const TRANS_MATCH = 'match';
    const TRANS_DONE = 'done';
}
