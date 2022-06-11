<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Title()
 * @method static static Description()
 * @method static static Status()
 * @method static static Priority()
 * @method static static Type()
 * @method static static Assignee()
 */
final class TicketProperty extends Enum
{
    const Title = 'Title';
    const Description = 'Description';
    const Status = 'Status';
    const Priority = 'Priority';
    const Type = 'Type';
    const Assignee = 'Assignee';
}
