<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property UserProfile $userProfile
 * @property Collection<Hub>|Hub[] $hubs
 * @property Collection<Moderator>|Moderator[] $moderators
 * @property Collection<Post>|Post[] $posts
 * @property Collection<Comment>|Comment[] $comments
 * @property Collection<Vote>|Vote[] $votes
 * @property Collection<Hub>|Hub[] $followedHubs
 * @property Collection<User>|User[] $followers
 * @property Collection<User>|User[] $following
 * @property Collection<Post>|Post[] $followedPosts
 *
 * @property integer $id
 * @property string $user_name
 * @property integer $follower_count
 *
 * @property string|null $email
 * @property string|Carbon|null $email_verified_at
 * @property string|null $phone
 * @property string|Carbon|null $phone_verified_at
 *
 * @property string $password
 * @property string $remember_token
 *
 * @property string|Carbon $created_at
 * @property string|Carbon $updated_at
 * @property string|Carbon|null $deleted_at
 *
 * @mixin Builder
 */
class User extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'user_name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function hubs(): HasMany
    {
        return $this->hasMany(Hub::class);
    }

    public function moderators(): HasMany
    {
        return $this->hasMany(Moderator::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function followedHubs(): BelongsToMany
    {
        return $this->belongsToMany(Hub::class, 'hub_followers')
            ->withTimestamps();
    }

    // Users that follow this user
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_followers', 'followed_id', 'follower_id')
            ->withTimestamps();
    }

    // Users that this user follows
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_followers', 'follower_id', 'followed_id')
            ->withTimestamps();
    }

    public function followedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_followers')
            ->withTimestamps();
    }
}
