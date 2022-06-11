<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Comment $comment)
    {
        return $user->hasRole(UserRole::Admin) ||
            $comment->whereHas(
                'user',
                fn ($query) => $query->whereKey($comment->id)
            )->exists();
    }

    public function store(User $user, Ticket $ticket)
    {
        return $user->hasRole(UserRole::Admin) ||
            $ticket->whereHas(
                'project.users',
                fn ($query) => $query->whereKey($user->id)
            )->exists();
    }

    public function destroy(User $user, Comment $comment)
    {
        return $user->hasRole(UserRole::Admin) ||
            $comment->whereHas(
                'user',
                fn ($query) => $query->whereKey($comment->id)
            )->exists();
    }
}
