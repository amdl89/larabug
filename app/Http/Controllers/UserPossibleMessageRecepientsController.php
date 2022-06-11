<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;

class UserPossibleMessageRecepientsController extends Controller
{
    public function index(User $user)
    {
        return UserResource::collection(
            User::query()
                ->whereKeyNot($user->id)
                ->with(['profile' => fn ($q2) => $q2->select('id', 'name')])
                ->select('id')
                ->withForeignKeys()
                ->get()
        );
    }
}
