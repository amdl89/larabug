<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OldestFirst()
 * @method static static LatestFirst()
 */
final class DateSortOrder extends Enum
{
    const OldestFirst = 'Oldest First';
    const LatestFirst =  'Latest First';
}
