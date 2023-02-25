<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
