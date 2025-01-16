<?php

namespace App\Models;

use App\Models\Enums\Gender;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property User $user
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $slug
 *
 * @property string $display_name
 * @property string|null $first_name
 * @property string|null $last_name
 *
 * @property string|null $bio
 * @property integer|null $age
 * @property string|null $date_of_birth
 * @property Gender|null $gender
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
class UserProfile extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'display_name',
        'first_name',
        'last_name',

        'bio',
        'date_of_birth',
        'gender',

        'background_color'
    ];
    protected $attributes = [
        'age'
    ];
    public function setSlug(): void
    {
        $this->slug = $this->user->user_name;
    }
    public function setAge(): void
    {
        $this->attributes['age'] = Carbon::parse($this->date_of_birth)->age;
    }
    public function setDefaultBackgroundColor(): void
    {
        $this->background_color = '#FFFFFF';
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
