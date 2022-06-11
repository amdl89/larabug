<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Unread()
 * @method static static Read()
 * @method static static ReadTrashed()
 * @method static static UnreadTrashed()
 * @method static static RemovedFromTrash()
 */
final class ReceivedMessageStatus extends Enum
{
    const Unread = 0;
    const Read = 1;
    const ReadTrashed = 2;
    const UnreadTrashed = 3;
}
