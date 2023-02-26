<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Exceptions\ModelHelperMethodException;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'content'
    ];
    
    /**
     * Post likes
     */
    public function likes() : BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'likes', 'post_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Post author
     */
    public function author() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope a query to fetch posts from users a given user follow.
     */
    public function scopeFromFollowsFor(Builder $query, User $user) : void
    {
        $query
            ->join('followers', 'followers.following_id', '=', 'posts.user_id')
            ->where('followers.follower_id', $user->id);
    }

    /**
     * Scope a query to order posts from the most recent.
     */
    public function scopeFromRecent(Builder $query) : void
    {
        $query->latest('posts.created_at');
    }

    /**
     * Scope a query to fetch posts related to given post ID.
     */
    public function scopeById(Builder $query, int $postId) : void
    {
        $query->where('posts.id', $postId);
    }

    /**
     * Scope a query to include post's total likes.
     */
    public function scopeIncludeTotalLikes(Builder $query) : void 
    {
        $query->withCount('likes as total_likes');
    }

    /**
     * Scope a query to status which determines if user has liked the post or not.
     */
    public function scopeIncludeLikeStatusFor(Builder $query, User $user) : void 
    {
        $query->withCount(['likes as liked' => fn(Builder $query) => $query->whereUserId($user->id)]);
    }

    //whether the authenticated user has liked the post

    /**
     * Like this post for a user
     *
     * @throws
     */
    public function addLikeFor(User $user) : void
    {
        if(empty($this->id)) {
            throw new ModelHelperMethodException;
        }

        if(! $this->isLikedBy($user)){ 
            $this->likes()->attach($user);
        }
    }

    /**
     * Unlike this post for a user
     *
     * @throws
     */
    public function removeLikeFor(User $user) : void
    {
        if(empty($this->id)) {
            throw new ModelHelperMethodException;
        }

        if($this->isLikedBy($user)){ 
            $this->likes()->detach($user);
        }
    }

    /**
     * Check if user already liked this post
     * 
     * @throws
     */
    public function isLikedBy(User $user) : bool
    {
        if(empty($this->id)) {
            throw new ModelHelperMethodException;
        }

        return $this->likes()->whereUserId($user->id)->exists();
    }
}
