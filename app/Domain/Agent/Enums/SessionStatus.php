<?php

namespace App\Domain\Agent\Enums;

enum SessionStatus: string
{
    case Active = 'active';
    case PausedHITL = 'paused_hitl';
    case Completed = 'completed';
    case Failed = 'failed';
}
