<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Draft()
 * @method static static Sent()
 * @method static static Trashed()
 */
final class SentMessageStatus extends Enum
{
    const Draft = 0;
    const Sent = 1;
    const Trashed = 2;
}
