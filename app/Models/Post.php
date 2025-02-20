<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property Hub $hub
 * @property User $user
 * @property Collection<User>|User[] $followers
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $hub_id
 * @property integer $follower_count
 *
 * @property string $title
 * @property string $slug
 * @property string $body
 * @property string|null $image
 * @property integer $vote_count
 *
 * @property string|Carbon $created_at
 * @property string|Carbon $updated_at
 * @property string|Carbon|null $deleted_at
 *
 * @mixin Builder
 */
class Post extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'title',
        'body',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function hub(): BelongsTo
    {
        return $this->belongsTo(Hub::class);
    }
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    public function votes(): MorphToMany
    {
        return $this->morphToMany(Vote::class, 'votable');
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_followers', 'post_id', 'user_id')
            ->withTimestamps();
    }
}
