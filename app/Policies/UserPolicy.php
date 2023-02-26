<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine if followable
     */
    public function follow(User $user, User $model): Response
    {
        return $user->id !== $model->id
        ? Response::allow()
        : Response::deny('You cannot follow yourself.');
    }

    /**
     * Determine if unfollowable
     */
    public function unfollow(User $user, User $model): Response
    {
        return $user->id !== $model->id
        ? Response::allow()
        : Response::deny('You cannot unfollow yourself.');
    }
}
