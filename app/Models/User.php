<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id');
    }

    /**
     * User that the user is following
     */
    public function follows() : BelongsToMany
    {
        //he is following them on follower_id
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id');
    }

    /**
     * User likes
     */
    public function likes() : HasManyThrough
    {
        return $this->hasManyThrough(Like::class, Post::class, 'user_id', 'post_id');
    }
}