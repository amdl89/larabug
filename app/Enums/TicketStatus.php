<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Open()
 * @method static static Resolved()
 * @method static static Reopended()
 * @method static static Verified()
 * @method static static Closed()
 */
final class TicketStatus extends Enum
{
    const Open = 0;
    const Resolved = 1;
    const Reopended = 2;
    const Verified = 3;
    const Closed = 4;
}
