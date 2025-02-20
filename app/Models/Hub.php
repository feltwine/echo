<?php

namespace App\Models;

use App\Models\Enums\HubStatus;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property User $user
 * @property Collection<Moderator>|Moderator[] $moderators
 * @property Collection<Post>|Post[] $posts
 * @property Collection<User>|User[] $followers
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $follower_count
 *
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property HubStatus $status
 *
 * @property string|null $avatar_path
 * @property string|null $background_path
 * @property string $background_color
 *
 * @property string|Carbon $created_at
 * @property string|Carbon $updated_at
 * @property string|Carbon|null $deleted_at
 *
 * @mixin Builder
 */
class Hub extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'status',
        'background_color'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function slug(): Attribute
    {
        return Attribute::make(
            get: function() {
                return Carbon::parse($this->name);
            }
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderators(): BelongsToMany
    {
        return $this->belongsToMany(Moderator::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function followers(): belongsToMany
    {
        return $this->BelongsToMany(User::class, 'hub_followers', 'hub_id', 'user_id')
            ->withTimestamps();
    }
}
