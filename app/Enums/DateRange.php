<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static All()
 * @method static static ThisWeek()
 * @method static static ThisMonth()
 * @method static static ThisYear()
 */
final class DateRange extends Enum
{
    const All = 'All Time';
    const ThisWeek = 'This Week';
    const ThisMonth = 'This Month';
    const ThisYear = 'This Year';
}
