<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentReplyPolicy
{
    use HandlesAuthorization;

    public function viewAll(User $user, Comment $comment)
    {
        return $user->hasRole(UserRole::Admin) ||
            $comment->whereHas(
                'ticket.project.users',
                fn ($query) => $query->whereKey($user->id)
            )->exists();
    }
}
