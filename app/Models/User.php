<?php

namespace App\Models;


// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Exceptions\CannotFollowSelf;
use App\Exceptions\ModelHelperMethodException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * User posts
     */
    public function posts() : HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * User followers
     */
    public function followers() : BelongsToMany
    {
        //They are following him on following_id
        return $this
            ->belongsToMany(User::class, 'followers', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    /**
     * User that the user is following
     */
    public function follows() : BelongsToMany
    {
        //he is following them on follower_id
        return $this
            ->belongsToMany(User::class, 'followers', 'follower_id', 'following_id')
            ->withTimestamps();
    }

//     followers
// `id`, `follower_id`, `following_id`,`created_at`, `updated_at`

    public function scopeFollowsPosts(Builder $query) : void
    {
        $query
            ->join('followers', 'followers.following_id', '=', 'users.id')
            ->join('posts', 'followers.follower_id', '=', 'posts.user_id');
    }
    
    public function followsss() : BelongsToMany
    {
        //he is following them on follower_id
        return $this
            ->belongsToMany(User::class, 'followers', 'follower_id', 'following_id')
            ->withTimestamps()
            ->join('posts', 'followers.follower_id', '=', 'posts.user_id');
    }

    /**
     * Scope a query to return posts from users this user follows
     */
    // public function followsPosts() : BelongsToMany
    // {
    //     return $this
    //         ->belongsToMany(Post::class, Follower::class, 'user_id', 'following_id');
    // }

    /**
     * User likes
     */
    public function likes() : HasManyThrough
    {
        return $this->hasManyThrough(Like::class, Post::class, 'user_id', 'post_id');
    }

    /**
     * Follow a user
     *
     * @throws
     */
    public function follow(User $user) : void
    {
        if(empty($this->id)) {
            throw new ModelHelperMethodException;
        }

        if(! $this->canFollow($user)) {
            throw new CannotFollowSelf;
        }

        if(! $this->isFollowing($user)){ 
            $this->follows()->attach($user);
        }
    }

    /**
     * Unfollow a user
     *
     * @throws
     */
    public function unfollow(User $user) : void
    {
        if(empty($this->id)) {
            throw new ModelHelperMethodException;
        }

        if($this->canFollow($user) && $this->isFollowing($user)){ 
            $this->follows()->detach($user);
        }
    }

    /**
     * Check if user is following a given user
     * @todo consider using accessors & mutators
     * 
     * @throws
     */
    public function isFollowing(User $user) : bool
    {
        if(empty($this->id)) {
            throw new ModelHelperMethodException;
        }

        return $this->follows()->whereFollowingId($user->id)->exists();
    }

    /**
     * Check if user can folow a given user
     * To prevent user following self
     * 
     * @throws
     */
    public function canFollow(User $user) : bool
    {
        if(empty($this->id)) {
            throw new ModelHelperMethodException;
        }

        return $this->id != $user->id;
    }

    /**
     * User like a given post
     * 
     *  @throws
     */
    public function likePost(Post $post) : void 
    {
        if(empty($this->id)) {
            throw new ModelHelperMethodException;
        }

        $post->addLikeFor($this);
    }

    /**
     * User unlike a given post
     * 
     *  @throws
     */
    public function unlikePost(Post $post) : void 
    {
        if(empty($this->id)) {
            throw new ModelHelperMethodException;
        }

        $post->removeLikeFor($this);
    }
}