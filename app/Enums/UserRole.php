<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Admin()
 * @method static static PM()
 * @method static static Developer()
 * @method static static Tester()
 */
final class UserRole extends Enum
{
    const Admin = 'Admin';
    const PM = 'Project Manager';
    const Dev = 'Developer';
    const Tester = 'Tester';
}
