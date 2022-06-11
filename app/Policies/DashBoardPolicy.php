<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashBoardPolicy
{
    use HandlesAuthorization;

    public function view(User $user)
    {
        return true;
    }
}
