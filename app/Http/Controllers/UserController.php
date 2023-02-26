<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Fetch a listing of the resource.
     */
    public function index()
    {
        $users = User::query()
        ->includeFollowersCount()
        ->includeFollowsCount()
        ->paginate();

        return new UserCollection($users);
    }

    /**
     * Fetch the specified resource.
     */
    public function show(int $id)
    {
        $user = User::query()
        ->whereId($id)
        ->includeFollowersCount()
        ->includeFollowsCount()
        ->firstOrFail();

        return new UserResource($user);
    }

    /**
     * Follow the given user.
     */
    public function follow(User $user)
    {
        $this->user()->follow($user);
        
        return response()->noContent();
    }

    /**
     * Unfollow the given user.
     */
    public function unfollow(User $user)
    {
        $this->user()->unfollow($user);
        
        return response()->noContent();
    }
}
