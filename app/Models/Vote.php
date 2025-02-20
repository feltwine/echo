<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property User $user
 *
 *  @property integer $id
 *  @property integer $user_id
 *
 *  @property string|Carbon $created_at
 *  @property string|Carbon $updated_at
 *  @property string|Carbon|null $deleted_at
 *
 * @mixin
 */
class Vote extends Model
{
    public function votable(): MorphTo
    {
        return $this->morphTo();
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
