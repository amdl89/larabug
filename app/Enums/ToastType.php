<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Success()
 * @method static static Error()
 * @method static static Warning()
 */
final class ToastType extends Enum
{
    const Success = 'success';
    const Info = 'info';
    const Error = 'error';
}
