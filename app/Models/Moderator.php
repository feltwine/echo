<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property Collection<User>|User[]|User $users
 * @property Collection<Hub>|Hub[]|Hub $Hubs
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $hub_id
 *
 * @property string|Carbon $created_at
 * @property string|Carbon $updated_at
 *
 * @mixin Builder
 */
class Moderator extends Model
{
    protected $fillable = [
        'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hub(): BelongsTo
    {
        return $this->belongsTo(Hub::class);
    }
}
