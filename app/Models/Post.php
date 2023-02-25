<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
            ->belongsToMany(User::class, 'likes', 'user_id', 'post_id')
            ->withTimestamps();
    }

    /**
     * Post author
     */
    public function author() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
